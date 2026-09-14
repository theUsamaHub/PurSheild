<?php

use App\Http\Middleware\MaintenanceModeMiddleware;
use App\Models\AdoptionApplication;
use App\Models\AdoptionListing;
use App\Models\CareStatusLog;
use App\Models\FurshieldNotification;
use App\Models\Review;
use App\Models\User;
use App\Support\VetNotifications;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ShelterPanelTest extends TestCase
{
    use RefreshDatabase;

    private User $shelter;

    private User $owner;

    private int $species;

    private int $breed;

    protected function migrateFreshUsing(): array
    {
        $names = [
            '0001_01_01_000000_create_users_table.php', '2024_01_01_000001_create_roles_and_permissions_tables.php',
            '2026_07_24_175801_create_settings_table.php', '2026_07_29_073215_create_notifications_table.php',
            '2026_09_12_000023_create_adoption_tables.php', '2026_09_12_000025_create_furshield_notifications_table.php',
            '2026_09_12_000026_create_reviews_table.php', '2026_09_13_115334_add_enhanced_fields_to_adoption_applications_table.php',
            '2026_09_13_120000_create_care_status_logs_table.php', '2026_09_14_000001_add_reply_to_reviews_table.php',
            '2026_09_14_000002_complete_shelter_workflows.php',
        ];
        foreach (glob(database_path('migrations/2026_09_12_*.php')) as $path) {
            if (substr(basename($path), 11, 6) <= '000017') {
                $names[] = basename($path);
            }
        }

        return ['--path' => array_map(fn ($name) => database_path('migrations/'.$name), $names), '--realpath' => true];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(Carbon::parse('2026-09-14 12:00:00'));
        $views = sys_get_temp_dir().'/pursheild-shelter-test-views';
        if (! is_dir($views)) {
            mkdir($views, 0777, true);
        }
        config(['view.compiled' => $views, 'session.driver' => 'array']);
        $this->withoutMiddleware(MaintenanceModeMiddleware::class);
        $this->shelter = User::factory()->create(['name' => 'Happy Paws Shelter', 'status' => 'active']);
        $this->owner = User::factory()->create(['name' => 'Ali Khan']);
        $role = DB::table('roles')->insertGetId(['name' => 'Animal Shelter', 'slug' => 'shelter']);
        $this->shelter->roles()->attach($role);
        $this->shelter->shelterProfile()->create(['shelter_name' => 'Happy Paws Shelter']);
        $this->species = DB::table('species')->insertGetId(['name' => 'Dog', 'status' => 'active']);
        $this->breed = DB::table('breeds')->insertGetId(['species_id' => $this->species, 'name' => 'Labrador']);
        $this->actingAs($this->shelter);
    }

    private function animal(array $data = []): AdoptionListing
    {
        return AdoptionListing::create(array_merge(['shelter_id' => $this->shelter->id, 'species_id' => $this->species, 'breed_id' => $this->breed, 'pet_name' => 'Bruno', 'age' => '2 years', 'status' => 'available', 'health_state' => 'healthy'], $data));
    }

    private function application(AdoptionListing $animal, array $data = []): AdoptionApplication
    {
        return AdoptionApplication::create(array_merge(['listing_id' => $animal->id, 'applicant_id' => $this->owner->id, 'message' => 'A loving family home', 'status' => 'pending'], $data));
    }

    private function care(AdoptionListing $animal, array $data = []): CareStatusLog
    {
        return CareStatusLog::create(array_merge(['shelter_id' => $this->shelter->id, 'listing_id' => $animal->id, 'animal_name' => $animal->pet_name, 'type' => 'feeding', 'notes' => 'Morning meal', 'log_date' => '2026-09-14', 'log_time' => '09:00:00', 'staff_name' => 'Ayesha', 'status' => 'completed'], $data));
    }

    private function animalData(array $data = []): array
    {
        return array_merge(['pet_name' => 'Luna', 'species_id' => $this->species, 'breed_id' => $this->breed, 'health_state' => 'healthy', 'status' => 'available'], $data);
    }

    public function test_photos_are_validated_and_removal_is_owned_and_preserves_other_photos(): void
    {
        Storage::fake('public');
        $this->post('/shelter/listings', $this->animalData(['images' => [UploadedFile::fake()->create('invalid.txt', 10)]]))->assertSessionHasErrors('images.0');
        $this->post('/shelter/listings', $this->animalData(['species_id' => ['bad']]))->assertSessionHasErrors('species_id');
        $pixel = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aDUcAAAAASUVORK5CYII=');
        $this->post('/shelter/listings', $this->animalData(['images' => [UploadedFile::fake()->createWithContent('first.png', $pixel), UploadedFile::fake()->createWithContent('second.png', $pixel)]]))->assertSessionHas('success');
        $animal = AdoptionListing::firstOrFail();
        $photos = $animal->images;
        $this->assertCount(2, $photos);
        Storage::disk('public')->assertExists($photos->pluck('image_path')->all());
        $foreign = $this->animal(['shelter_id' => $this->owner->id])->images()->create(['image_path' => 'private.jpg']);
        $this->put('/shelter/listings/'.$animal->id, $this->animalData(['remove_images' => [$foreign->id]]))->assertSessionHasErrors('remove_images.0');
        $this->put('/shelter/listings/'.$animal->id, $this->animalData(['remove_images' => [$photos[0]->id]]))->assertSessionHas('success');
        Storage::disk('public')->assertMissing($photos[0]->image_path);
        Storage::disk('public')->assertExists($photos[1]->image_path);
        $this->assertSame(1, $animal->images()->count());
        $this->assertNotNull($foreign->fresh());
    }

    public function test_owner_application_validates_then_blocks_duplicates_and_reserved_animals(): void
    {
        $role = DB::table('roles')->insertGetId(['name' => 'Pet Owner', 'slug' => 'owner']);
        $this->owner->roles()->attach($role);
        $animal = $this->animal();
        $this->actingAs($this->owner);
        $url = route('owner.adoption.apply', $animal);
        $this->post($url, [])->assertSessionHasErrors('message');
        $this->post($url, ['message' => 'A loving family home'])->assertSessionHas('success');
        $this->post($url, ['message' => 'Duplicate'])->assertSessionHas('error');
        $this->assertSame(1, $animal->applications()->count());
        $this->assertDatabaseHas('furshield_notifications', ['user_id' => $this->shelter->id, 'title' => 'New adoption request', 'is_read' => false]);
        $this->assertSame(1, \App\Models\FurshieldNotification::where('user_id', $this->shelter->id)->count());
        $reserved = $this->animal(['status' => 'pending']);
        $this->post(route('owner.adoption.apply', $reserved), ['message' => 'A family home'])->assertSessionHas('error');
        $this->assertSame(0, $reserved->applications()->count());
    }

    public function test_every_shelter_page_and_detail_renders_with_linked_data(): void
    {
        $animal = $this->animal();
        $application = $this->application($animal);
        $log = $this->care($animal);
        foreach (['dashboard', 'listings', 'listings/create', 'care-status', 'care-status/create', 'applications', 'adoption-history', 'notifications', 'reviews'] as $page) {
            $this->get('/shelter/'.$page)->assertOk();
        }
        foreach (['listings/'.$animal->id, 'listings/'.$animal->id.'/edit', 'applications/'.$application->id, 'care-status/'.$log->id, 'care-status/'.$log->id.'/edit'] as $page) {
            $this->get('/shelter/'.$page)->assertOk();
        }
        $this->get('/profile')->assertOk()->assertSee('Shelter Profile');
        $this->get('/shelter/listings')->assertSee('Healthy')->assertSee('Available')->assertDontSee('Species not found');
    }

    public function test_animal_filters_combine_sort_and_preserve_pagination(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $this->animal(['pet_name' => 'Bruno '.$i]);
        }
        $this->animal(['pet_name' => 'Other', 'health_state' => 'under_treatment']);
        $this->animal(['pet_name' => 'Bruno private', 'shelter_id' => $this->owner->id]);
        $url = '/shelter/listings?search=Bruno&species_id='.$this->species.'&breed_id='.$this->breed.'&health_state=healthy&status=available&sort=name';
        $this->get($url)->assertOk()->assertViewHas('listings', fn ($items) => $items->total() === 10 && $items->count() === 8 && str_contains($items->nextPageUrl(), 'health_state=healthy'));
        $this->get('/shelter/listings?search=0')->assertViewHas('listings', fn ($items) => $items->total() === 1);
        $this->get('/shelter/listings?health_state=under_treatment')->assertViewHas('listings', fn ($items) => $items->total() === 1);
        $this->get('/shelter/listings?search[]=bad')->assertSessionHasErrors('search');
        $this->get('/shelter/listings?status=wrong')->assertSessionHasErrors('status');
    }

    public function test_animal_creation_and_edit_validate_species_breed_and_health(): void
    {
        $cat = DB::table('species')->insertGetId(['name' => 'Cat']);
        $catBreed = DB::table('breeds')->insertGetId(['name' => 'Persian', 'species_id' => $cat]);
        $this->post('/shelter/listings', $this->animalData(['breed_id' => $catBreed]))->assertSessionHasErrors('breed_id');
        $this->post('/shelter/listings', $this->animalData(['health_state' => 'invalid']))->assertSessionHasErrors('health_state');
        $this->post('/shelter/listings', $this->animalData(['status' => 'adopted']))->assertSessionHasErrors('status');
        $this->post('/shelter/listings', $this->animalData())->assertSessionHas('success');
        $animal = AdoptionListing::firstOrFail();
        $this->assertSame($this->shelter->id, $animal->shelter_id);
        $this->put('/shelter/listings/'.$animal->id, $this->animalData(['pet_name' => 'Luna updated', 'health_state' => 'vaccination_due']))->assertSessionHas('success');
        $this->assertSame('Luna updated', $animal->fresh()->pet_name);
        $this->get('/shelter/listings?species_id='.$this->species.'&breed_id='.$catBreed)->assertSessionHasErrors('breed_id');
    }

    public function test_foreign_animal_actions_are_forbidden(): void
    {
        $animal = $this->animal(['shelter_id' => $this->owner->id]);
        $this->get('/shelter/listings/'.$animal->id)->assertForbidden();
        $this->get('/shelter/listings/'.$animal->id.'/edit')->assertForbidden();
        $this->put('/shelter/listings/'.$animal->id, $this->animalData())->assertForbidden();
        $this->delete('/shelter/listings/'.$animal->id)->assertForbidden();
    }

    public function test_archiving_preserves_history_and_rejects_active_requests(): void
    {
        $animal = $this->animal();
        $log = $this->care($animal);
        $this->delete('/shelter/listings/'.$animal->id)->assertSessionHas('success');
        $this->assertSame('inactive', $animal->fresh()->status);
        $this->assertNotNull($log->fresh());
        $active = $this->animal();
        $this->application($active);
        $this->delete('/shelter/listings/'.$active->id)->assertSessionHasErrors('listing');
        $this->assertSame('available', $active->fresh()->status);
    }

    public function test_care_crud_keeps_animal_staff_time_and_followup(): void
    {
        $animal = $this->animal();
        $data = ['listing_id' => $animal->id, 'type' => 'vaccination', 'notes' => 'Vaccination scheduled', 'log_date' => '2026-09-14', 'log_time' => '13:30', 'staff_name' => 'Dr. Ahmed', 'status' => 'follow_up', 'due_date' => '2026-09-21'];
        $this->post('/shelter/care-status', $data)->assertSessionHas('success');
        $log = CareStatusLog::firstOrFail();
        $this->assertSame('13:30:00', $log->log_time);
        $this->assertSame('Bruno', $log->animal_name);
        $this->put('/shelter/care-status/'.$log->id, array_merge($data, ['status' => 'completed', 'log_time' => '09:30', 'notes' => 'Vaccination completed']))->assertSessionHas('success');
        $this->assertSame('completed', $log->fresh()->status);
        $this->get('/shelter/care-status/'.$log->id)->assertOk()->assertSee('Vaccination completed');
        $this->delete('/shelter/care-status/'.$log->id)->assertSessionHas('success');
        $this->assertNull($log->fresh());
    }

    public function test_care_rejects_foreign_animals_invalid_dates_and_missing_fields(): void
    {
        $foreign = $this->animal(['shelter_id' => $this->owner->id]);
        $this->post('/shelter/care-status', ['listing_id' => $foreign->id])->assertSessionHasErrors(['listing_id', 'type', 'notes', 'log_date', 'log_time', 'staff_name', 'status']);
        $animal = $this->animal();
        $data = ['listing_id' => $animal->id, 'type' => 'medical', 'notes' => 'Checkup', 'log_date' => '2026-09-14', 'log_time' => '10:00', 'staff_name' => 'Vet', 'status' => 'follow_up'];
        $this->post('/shelter/care-status', $data)->assertSessionHasErrors('due_date');
        $this->post('/shelter/care-status', $data + ['due_date' => '2026-09-01'])->assertSessionHasErrors('due_date');
        $this->post('/shelter/care-status', array_merge($data, ['status' => 'completed', 'log_date' => '2026-09-20']))->assertSessionHasErrors('log_date');
        $foreignLog = $this->care($foreign, ['shelter_id' => $this->owner->id]);
        $this->get('/shelter/care-status/'.$foreignLog->id)->assertForbidden();
        $this->put('/shelter/care-status/'.$foreignLog->id, $data)->assertForbidden();
        $this->delete('/shelter/care-status/'.$foreignLog->id)->assertForbidden();
    }

    public function test_care_filters_search_dates_status_type_and_pagination(): void
    {
        $animal = $this->animal();
        for ($i = 0; $i < 12; $i++) {
            $this->care($animal, ['notes' => 'Meal '.$i]);
        }
        $this->care($animal, ['type' => 'medical', 'notes' => 'Checkup', 'status' => 'pending', 'log_date' => '2026-10-05']);
        $this->get('/shelter/care-status?search=Meal&type=feeding&status=completed&period=today&listing_id='.$animal->id)->assertOk()->assertViewHas('logs', fn ($items) => $items->total() === 12 && $items->count() === 10 && str_contains($items->nextPageUrl(), 'type=feeding'));
        $this->get('/shelter/care-status?date_from=2026-10-01&date_to=2026-10-10')->assertViewHas('logs', fn ($items) => $items->total() === 1);
        $this->get('/shelter/care-status?date_from=2026-10-10&date_to=2026-10-01')->assertSessionHasErrors('date_to');
        $this->get('/shelter/care-status?search=0')->assertViewHas('logs', fn ($items) => $items->total() === 2);
    }

    public function test_request_filters_and_selected_panel_are_scoped(): void
    {
        $animal = $this->animal();
        for ($i = 0; $i < 9; $i++) {
            $this->application($animal, ['status' => 'reviewing']);
        }
        $this->application($animal, ['status' => 'pending']);
        $foreign = $this->application($this->animal(['shelter_id' => $this->owner->id]));
        $this->get('/shelter/applications?search=Ali&status=reviewing')->assertOk()->assertViewHas('applications', fn ($items) => $items->total() === 9 && $items->count() === 8 && str_contains($items->nextPageUrl(), 'status=reviewing'));
        $this->get('/shelter/applications?selected='.$foreign->id)->assertNotFound();
        $this->get('/shelter/applications?panel=closed')->assertViewHas('selected', null);
        $this->get('/shelter/applications/'.$foreign->id)->assertForbidden();
        $this->put('/shelter/applications/'.$foreign->id.'/status', ['status' => 'approved'])->assertForbidden();
        $this->post('/shelter/applications/'.$foreign->id.'/finalize')->assertForbidden();
    }

    public function test_review_approve_finalize_flow_is_atomic_and_closes_competitors(): void
    {
        $animal = $this->animal();
        $chosen = $this->application($animal);
        $other = $this->application($animal, ['status' => 'reviewing']);
        $this->put('/shelter/applications/'.$chosen->id.'/status', ['status' => 'reviewing'])->assertSessionHas('success');
        $this->assertSame('reviewing', $chosen->fresh()->status);
        $this->put('/shelter/applications/'.$chosen->id.'/status', ['status' => 'approved', 'shelter_response' => 'Welcome'])->assertSessionHas('success');
        $this->assertSame('pending', $animal->fresh()->status);
        $this->put('/shelter/applications/'.$other->id.'/status', ['status' => 'approved'])->assertSessionHasErrors('status');
        $this->put('/shelter/listings/'.$animal->id, $this->animalData())->assertSessionHasErrors('status');
        $this->post('/shelter/applications/'.$chosen->id.'/finalize')->assertSessionHas('success');
        $this->assertSame('completed', $chosen->fresh()->status);
        $this->assertNotNull($chosen->fresh()->completed_at);
        $this->assertSame('adopted', $animal->fresh()->status);
        $this->assertSame('rejected', $other->fresh()->status);
        $this->post('/shelter/applications/'.$chosen->id.'/finalize')->assertSessionHasErrors('status');
        $this->put('/shelter/applications/'.$chosen->id.'/status', ['status' => 'rejected'])->assertSessionHasErrors('status');
    }

    public function test_pending_or_rejected_requests_cannot_be_finalized_and_rejection_releases_reservation(): void
    {
        $animal = $this->animal();
        $application = $this->application($animal);
        $this->post('/shelter/applications/'.$application->id.'/finalize')->assertSessionHasErrors('status');
        $this->put('/shelter/applications/'.$application->id.'/status', ['status' => 'approved'])->assertSessionHas('success');
        $this->put('/shelter/applications/'.$application->id.'/status', ['status' => 'rejected'])->assertSessionHas('success');
        $this->assertSame('available', $animal->fresh()->status);
        $this->post('/shelter/applications/'.$application->id.'/finalize')->assertSessionHasErrors('status');
        $this->put('/shelter/applications/'.$application->id.'/status', ['status' => 'reviewing'])->assertSessionHasErrors('status');
    }

    public function test_history_filters_use_decision_dates_and_include_legacy_adoptions(): void
    {
        $animal = $this->animal(['status' => 'adopted']);
        $this->application($animal, ['status' => 'completed', 'completed_at' => '2026-09-02 10:00:00']);
        $this->application($animal, ['status' => 'approved', 'decided_at' => '2026-08-05 10:00:00']);
        $this->application($this->animal(), ['status' => 'rejected', 'decided_at' => '2026-09-10 10:00:00']);
        $this->application($this->animal());
        $this->get('/shelter/adoption-history')->assertOk()->assertViewHas('applications', fn ($items) => $items->total() === 3);
        $this->get('/shelter/adoption-history?status=completed&date_from=2026-09-01&date_to=2026-09-30&search=Ali&species_id='.$this->species)->assertViewHas('applications', fn ($items) => $items->total() === 1);
        $this->get('/shelter/adoption-history?status=pending')->assertSessionHasErrors('status');
    }

    public function test_dashboard_counts_and_attention_reflect_actual_records(): void
    {
        $animal = $this->animal(['health_state' => 'under_treatment']);
        $this->application($animal);
        $this->care($animal, ['status' => 'pending']);
        $this->animal(['status' => 'adopted']);
        $this->animal(['shelter_id' => $this->owner->id]);
        $this->get('/shelter/dashboard')->assertOk()->assertViewHas('stats', fn ($stats) => $stats['total'] === 2 && $stats['pending'] === 1 && $stats['under_treatment'] === 1 && $stats['adopted'] === 1);
    }

    public function test_notifications_merge_and_read_only_own_records(): void
    {
        $notice = FurshieldNotification::create(['user_id' => $this->shelter->id, 'title' => 'New request', 'message' => 'A new application', 'type' => 'application', 'is_read' => false]);
        $foreign = FurshieldNotification::create(['user_id' => $this->owner->id, 'title' => 'Private notice', 'message' => 'Private', 'type' => 'application', 'is_read' => false]);
        $this->shelter->notifications()->create(['id' => (string) Str::uuid(), 'type' => 'test', 'data' => ['title' => 'System update']]);
        $this->get('/shelter/notifications')->assertOk()->assertSee('System update')->assertSee('New request')->assertDontSee('Private notice');
        $this->patch('/shelter/notifications/practice/'.$foreign->id.'/read')->assertNotFound();
        $this->patch('/shelter/notifications/practice/'.$notice->id.'/read')->assertSessionHas('success');
        $this->get('/shelter/notifications?status=unread')->assertViewHas('notifications', fn ($items) => $items->total() === 1);
        $this->patch('/shelter/notifications/read-all')->assertSessionHas('success');
        $this->assertSame(0, VetNotifications::unreadCount($this->shelter));
        $this->assertFalse($foreign->fresh()->is_read);
    }

    public function test_existing_adoption_decisions_reconcile_listing_status_without_changing_applications(): void
    {
        $completedAnimal = $this->animal();
        $completed = $this->application($completedAnimal, ['status' => 'completed']);
        $approvedAnimal = $this->animal();
        $approved = $this->application($approvedAnimal, ['status' => 'approved']);
        $migration = require database_path('migrations/2026_09_14_000003_align_shelter_adoption_statuses.php');
        $migration->up();
        $this->assertSame('adopted', $completedAnimal->fresh()->status);
        $this->assertSame('pending', $approvedAnimal->fresh()->status);
        $this->assertSame('completed', $completed->fresh()->status);
        $this->assertSame('approved', $approved->fresh()->status);
    }

    public function test_review_filters_search_rating_sort_and_scope(): void
    {
        $animal = $this->animal();
        $foreign = $this->animal(['shelter_id' => $this->owner->id]);
        foreach ([[$animal, 5, 'Wonderful care'], [$animal, 2, 'Needs improvement'], [$foreign, 5, 'Private care']] as [$listing,$rating,$comment]) {
            Review::create(['user_id' => $this->owner->id, 'reviewable_type' => AdoptionListing::class, 'reviewable_id' => $listing->id, 'rating' => $rating, 'comment' => $comment]);
        }
        $this->get('/shelter/reviews?search=care&rating=5')->assertOk()->assertViewHas('reviews', fn ($items) => $items->total() === 1);
        $this->get('/shelter/reviews?sort=lowest')->assertViewHas('reviews', fn ($items) => $items->first()->rating === 2);
        $this->get('/shelter/reviews?search=Bruno')->assertViewHas('reviews', fn ($items) => $items->total() === 2);
        $this->get('/shelter/reviews?rating=6')->assertSessionHasErrors('rating');
    }

    public function test_completed_care_cannot_be_recorded_at_a_future_time(): void
    {
        $animal = $this->animal();
        $this->post('/shelter/care-status',['listing_id' => $animal->id, 'type' => 'medical', 'notes' => 'Checkup', 'log_date' => '2026-09-14', 'log_time' => '23:59', 'staff_name' => 'Vet', 'status' => 'completed'])->assertSessionHasErrors('log_time');
        $this->assertSame(0,CareStatusLog::count());
    }
}
