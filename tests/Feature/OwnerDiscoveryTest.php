<?php

use App\Http\Middleware\MaintenanceModeMiddleware;
use App\Models\AdoptionListing;
use App\Models\Appointment;
use App\Models\Breed;
use App\Models\CareContent;
use App\Models\MedicalDocument;
use App\Models\Pet;
use App\Models\Review;
use App\Models\Specialization;
use App\Models\Species;
use App\Models\Treatment;
use App\Models\User;
use App\Models\VetAvailability;
use App\Support\OwnerDiscovery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OwnerDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $other;

    private Species $species;

    private Breed $breed;

    protected function migrateFreshUsing(): array
    {
        $names = ['0001_01_01_000000_create_users_table.php', '2024_01_01_000001_create_roles_and_permissions_tables.php', '2026_07_24_175801_create_settings_table.php', '2026_07_29_073215_create_notifications_table.php', '2026_09_13_000001_add_primary_and_sort_order_to_pet_images_table.php', '2026_09_13_115334_add_enhanced_fields_to_adoption_applications_table.php', '2026_09_13_120000_create_care_status_logs_table.php', '2026_09_14_000001_add_reply_to_reviews_table.php', '2026_09_14_000002_complete_shelter_workflows.php', '2026_09_15_000001_add_owner_discovery_features.php', '2026_09_15_000002_expand_pet_care_categories.php'];
        foreach (glob(database_path('migrations/2026_09_12_*.php')) as $file) {
            $names[] = basename($file);
        }

        return ['--path' => array_map(fn ($n) => database_path('migrations/'.$n), $names), '--realpath' => true];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo('2026-09-15 08:00:00');
        $compiled = sys_get_temp_dir().'/owner-discovery-test-views';
        if (! is_dir($compiled)) {
            mkdir($compiled, 0777, true);
        }config(['view.compiled' => $compiled, 'session.driver' => 'array']);
        $this->withoutMiddleware(MaintenanceModeMiddleware::class);
        foreach (['owner', 'vet', 'shelter'] as $slug) {
            DB::table('roles')->insert(['name' => ucfirst($slug), 'slug' => $slug]);
        }
        $this->owner = $this->user('Ayesha Khan', 'owner');
        $this->other = $this->user('Other Owner', 'owner');
        $this->species = Species::create(['name' => 'Dog', 'status' => 'active']);
        $this->breed = Breed::create(['name' => 'Golden Retriever', 'species_id' => $this->species->id]);
        $this->actingAs($this->owner);
    }

    private function user(string $name, string $role): User
    {
        $u = User::factory()->create(['name' => $name, 'status' => 'active']);
        $u->assignRole($role);

        return $u;
    }

    private function pet(array $v = []): Pet
    {
        return Pet::create(array_merge(['owner_id' => $this->owner->id, 'species_id' => $this->species->id, 'breed_id' => $this->breed->id, 'name' => 'Bruno', 'gender' => 'male', 'date_of_birth' => '2024-03-14', 'weight' => 28], $v));
    }

    private function vet(string $name = 'Dr. Ahmed Raza', array $profile = []): User
    {
        $v = $this->user($name, 'vet');
        $v->vetProfile()->create(array_merge(['clinic_name' => 'Happy Paws Clinic', 'clinic_address' => 'DHA Karachi', 'city' => 'Karachi', 'is_verified' => true, 'latitude' => 24.86, 'longitude' => 67.01, 'consultation_fee' => 1500], $profile));
        VetAvailability::create(['vet_id' => $v->id, 'day_of_week' => 'tuesday', 'start_time' => '09:00', 'end_time' => '17:00', 'is_available' => true]);

        return $v;
    }

    private function shelter(): User
    {
        $s = $this->user('Happy Paws Shelter', 'shelter');
        $s->shelterProfile()->create(['shelter_name' => 'Happy Paws Shelter', 'city' => 'Karachi']);

        return $s;
    }

    private function listing(User $s, array $v = []): AdoptionListing
    {
        return AdoptionListing::create(array_merge(['shelter_id' => $s->id, 'species_id' => $this->species->id, 'breed_id' => $this->breed->id, 'pet_name' => 'Max', 'age' => '2 years', 'gender' => 'male', 'status' => 'available', 'health_state' => 'healthy'], $v));
    }

    private function content(array $v = []): CareContent
    {
        return CareContent::create(array_merge(['title' => 'How Much Should Your Dog Eat?', 'category' => 'feeding', 'content_type' => 'article', 'content' => 'Learn the right portion sizes for your pet.', 'status' => 'active'], $v));
    }

    public function test_health_filters_timeline_and_pdf_include_all_owned_sources(): void
    {
        $pet = $this->pet();
        $vet = $this->vet();
        for ($i = 0; $i < 9; $i++) {
            $pet->healthRecords()->create(['record_type' => 'allergy', 'record_date' => '2026-09-14', 'description' => 'Chicken '.$i]);
        }
        $pet->vaccinations()->create(['vaccine_name' => 'Rabies', 'vaccination_date' => '2026-09-12', 'next_due_date' => '2027-09-12']);
        $appointment = Appointment::create(['pet_id' => $pet->id, 'owner_id' => $this->owner->id, 'vet_id' => $vet->id, 'appointment_date' => '2026-09-10', 'appointment_time' => '10:00', 'status' => 'completed']);
        Treatment::create(['appointment_id' => $appointment->id, 'pet_id' => $pet->id, 'vet_id' => $vet->id, 'diagnosis' => 'Skin Infection', 'treatment' => 'Antibiotics prescribed']);
        $foreign = $this->pet(['owner_id' => $this->other->id, 'name' => 'Private Pet']);
        $foreign->healthRecords()->create(['record_type' => 'illness', 'record_date' => '2026-09-14', 'description' => 'Private diagnosis']);
        $this->get('/owner/health-records')->assertOk()->assertSee('Health Timeline')->assertSee('Skin Infection')->assertDontSee('Private diagnosis');
        $this->get('/owner/pets/'.$pet->id.'/health?type=allergy&search=Chicken')->assertOk()->assertViewHas('records', fn ($r) => $r->total() === 9 && $r->count() === 8 && str_contains($r->nextPageUrl(), 'type=allergy'));
        $this->get('/owner/pets/'.$pet->id.'/health?search=0')->assertViewHas('records', fn ($r) => $r->getCollection()->where('type', 'allergy')->count() === 1);
        $this->get('/owner/pets/'.$pet->id.'/health?type=treatment')->assertSee('Skin Infection');
        $this->get('/owner/pets/'.$foreign->id.'/health')->assertForbidden();
        $this->get('/owner/pets/'.$pet->id.'/health?record=record-99999')->assertNotFound();
        $pdf = $this->get('/owner/pets/'.$pet->id.'/health/download')->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-1.4', $pdf->getContent());
        $this->assertStringContainsString('Skin Infection', $pdf->getContent());
        $this->assertStringNotContainsString('Private diagnosis', $pdf->getContent());
        $this->get('/owner/pets/'.$foreign->id.'/health/download')->assertForbidden();
    }

    public function test_health_validation_private_upload_and_file_ownership(): void
    {
        Storage::fake('local');
        $pet = $this->pet();
        $url = '/owner/pets/'.$pet->id;
        $this->post($url.'/health', ['record_type' => 'invented', 'record_date' => '2026-09-16'])->assertSessionHasErrors(['record_type', 'record_date', 'description']);
        $this->post($url.'/vaccinations', ['vaccine_name' => 'Rabies', 'vaccination_date' => '2023-01-01'])->assertSessionHasErrors('vaccination_date');
        $this->post($url.'/vaccinations', ['vaccine_name' => 'Rabies', 'vaccination_date' => '2026-09-14', 'next_due_date' => '2026-09-13'])->assertSessionHasErrors('next_due_date');
        $file = UploadedFile::fake()->createWithContent('blood.pdf', "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n%%EOF");
        $this->post($url.'/health', ['record_type' => 'lab_report', 'description' => 'Blood Test', 'record_date' => '2026-09-14', 'file' => $file])->assertRedirect($url.'/health');
        $document = MedicalDocument::firstOrFail();
        $this->assertStringStartsWith('medical-documents/', $document->file_path);
        Storage::disk('local')->assertExists($document->file_path);
        $this->get($url.'/documents/'.$document->id)->assertOk()->assertDownload('blood.pdf');
        $vet = $this->vet();
        Appointment::create(['owner_id'=>$this->owner->id,'pet_id'=>$pet->id,'vet_id'=>$vet->id,'appointment_date'=>'2026-09-15','appointment_time'=>'10:00','status'=>'approved']);
        $this->actingAs($vet)->get(route('vet.patients.document',[$pet,$document]))->assertOk()->assertDownload('blood.pdf');
        $this->actingAs($this->owner);
        $foreign = $this->pet(['owner_id' => $this->other->id]);
        $this->get('/owner/pets/'.$foreign->id.'/documents/'.$document->id)->assertForbidden();
        $second = $this->pet(['name' => 'Luna']);
        $this->get('/owner/pets/'.$second->id.'/documents/'.$document->id)->assertNotFound();
        $this->post($url.'/documents', ['file' => UploadedFile::fake()->create('bad.exe', 10, 'application/octet-stream')])->assertSessionHasErrors('file');
    }

    public function test_care_filters_real_content_video_and_view_counts(): void
    {
        $article = $this->content(['title' => 'Feeding 0', 'content' => '<p>Actual article content.</p><script>alert(1)</script>']);
        $this->content(['title' => 'Hidden Article', 'status' => 'inactive']);
        $groom = $this->content(['title' => 'Cat Grooming', 'category' => 'hygiene']);
        $video = $this->content(['title' => 'Vaccination Guide', 'category' => 'health', 'content_type' => 'video', 'media_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ']);
        $this->get('/owner/care')->assertOk()->assertSee('Featured Articles')->assertSee('Educational Videos')->assertDontSee('Hidden Article');
        $this->get('/owner/care?search=0')->assertSee('Feeding 0')->assertViewHas('articles', fn ($a) => $a->count() === 1);
        $this->get('/owner/care?category=grooming')->assertViewHas('articles', fn ($a) => $a->count() === 1 && $a->first()->id === $groom->id);
        $this->get('/owner/care?category=vaccination&content_type=video')->assertViewHas('careContents', fn ($a) => $a->total() === 1);
        $this->get('/owner/care/'.$article->id)->assertSee('Actual article content.')->assertDontSee('<script>alert(1)</script>', false);
        $this->get('/owner/care/'.$article->id)->assertOk();
        $this->assertEquals(1, $article->fresh()->views_count);
        $this->get('/owner/care/'.$video->id)->assertSee('youtube-nocookie.com/embed/dQw4w9WgXcQ');
        $this->content(['title'=>'Daily brushing','category'=>'grooming']);
        $dedicated = $this->content(['title'=>'Preventive care','category'=>'vaccination']);
        $this->get('/owner/care?category=grooming')->assertViewHas('articles',fn($items)=>$items->count()===2);
        $this->get('/owner/care?category=vaccination')->assertViewHas('articles',fn($items)=>$items->count()===1&&$items->first()->id===$dedicated->id);
        $this->get('/owner/care?search[]=bad&category=bad')->assertSessionHasErrors(['search', 'category']);
    }

    public function test_vet_filters_distance_availability_and_public_visibility(): void
    {
        $vet = $this->vet('Dr. Ahmed Raza', ['online_consultation' => true, 'emergency_services' => true]);
        $far = $this->vet('Dr. Far', ['latitude' => 31.52, 'longitude' => 74.35, 'city' => 'Lahore']);
        $inactive = $this->vet('Inactive Vet');
        $inactive->update(['status' => 'inactive']);
        $unverified = $this->vet('Unverified Vet', ['is_verified' => false]);
        $special = Specialization::create(['name' => 'General Vet', 'status' => 'active']);
        $vet->specializations()->attach($special);
        Review::create(['user_id' => $this->other->id, 'reviewable_type' => User::class, 'reviewable_id' => $vet->id, 'rating' => 5, 'comment' => 'Excellent']);
        $url = '/owner/browse-vets?search=Happy&location=Karachi&specialization_id='.$special->id.'&rating=4&online=1&emergency=1&availability=today&latitude=24.86&longitude=67.01&distance=10&sort=nearest';
        $this->get($url)->assertOk()->assertViewHas('vets', fn ($v) => $v->total() === 1 && $v->first()->id === $vet->id && $v->first()->distance === 0.0)->assertSee('Available Today')->assertDontSee('Inactive Vet')->assertDontSee('Unverified Vet');
        $this->get('/owner/browse-vets/'.$inactive->id)->assertNotFound();
        $this->get('/owner/browse-vets/'.$unverified->id)->assertNotFound();
        $this->get('/owner/browse-vets?latitude=100&longitude=200&rating=8')->assertSessionHasErrors(['latitude', 'longitude', 'rating']);
        $this->get('/owner/browse-vets?compare[]='.$vet->id.'&compare[]='.$far->id)->assertOk()->assertViewHas('comparison', fn ($c) => $c->count() === 2);
    }

    public function test_booking_rejects_taken_blocked_and_outside_schedule_times(): void
    {
        $vet = $this->vet();
        $pet = $this->pet();
        VetAvailability::create(['vet_id' => $vet->id, 'day_of_week' => 'tuesday', 'start_time' => '10:00', 'end_time' => '11:00', 'is_available' => false]);
        $data = ['pet_id' => $pet->id, 'vet_id' => $vet->id, 'appointment_date' => '2026-09-15', 'appointment_time' => '09:00', 'reason' => 'Health check'];
        $this->post('/owner/appointments', $data)->assertRedirect('/owner/appointments');
        $this->post('/owner/appointments', $data)->assertSessionHasErrors('appointment_time');
        foreach (['07:00', '10:00', '19:00', '09:15'] as $time) {
            $this->post('/owner/appointments', array_merge($data, ['appointment_time' => $time]))->assertSessionHasErrors('appointment_time');
        }
        $foreign = $this->pet(['owner_id' => $this->other->id]);
        $this->post('/owner/appointments', array_merge($data, ['pet_id' => $foreign->id]))->assertSessionHasErrors('pet_id');
        $this->assertDatabaseCount('appointments', 1);
        $this->get('/owner/appointments/create?pet_id='.$pet->id.'&vet_id='.$vet->id)->assertOk();
    }

    public function test_adoption_filters_age_health_breed_gender_city_and_pagination(): void
    {
        $s = $this->shelter();
        for ($i = 0; $i < 9; $i++) {
            $this->listing($s, ['pet_name' => 'Max '.$i, 'health_state' => 'under_treatment', 'health_status' => 'Recovering well']);
        }
        $baby = $this->listing($s, ['pet_name' => 'Baby', 'age' => '8 months']);
        $this->listing($s, ['pet_name' => 'Private Listing', 'status' => 'inactive']);
        $pending = $this->listing($s, ['pet_name' => 'Pending Pet', 'status' => 'pending']);
        $this->get('/owner/browse-adoption?search=Max&age=young&health_status=under_treatment&gender=male&location=Karachi&breed_id='.$this->breed->id)->assertOk()->assertViewHas('listings', fn ($v) => $v->total() === 9 && $v->count() === 8 && str_contains($v->nextPageUrl(), 'health_status=under_treatment'))->assertDontSee('Private Listing');
        $this->get('/owner/browse-adoption?age=baby')->assertViewHas('listings', fn ($v) => $v->total() === 1 && $v->first()->id === $baby->id);
        $this->get('/owner/browse-adoption/'.$pending->id)->assertOk();
        $this->assertEquals(18, OwnerDiscovery::ageMonths('1.5 Years'));
        $this->assertNull(OwnerDiscovery::ageMonths('unknown'));
        $this->get('/owner/browse-adoption?gender=bad&age=bad')->assertSessionHasErrors(['gender', 'age']);
    }

    public function test_favorites_are_persistent_idempotent_and_scoped(): void
    {
        $vet = $this->vet();
        $listing = $this->listing($this->shelter());
        foreach ([['vet', $vet->id], ['pet', $listing->id]] as [$kind,$id]) {
            $url = '/owner/favorites/'.$kind.'/'.$id;
            $this->postJson($url, ['saved' => true])->assertOk()->assertJson(['saved' => true]);
            $this->postJson($url, ['saved' => true])->assertOk();
        }
        $this->assertDatabaseCount('owner_favorites', 2);
        $this->get('/owner/browse-vets?favorites=1')->assertViewHas('vets', fn ($v) => $v->total() === 1);
        $this->actingAs($this->other)->get('/owner/browse-adoption?favorites=1')->assertViewHas('listings', fn ($v) => $v->total() === 0);
        $this->actingAs($this->owner)->postJson('/owner/favorites/pet/'.$listing->id, ['saved' => false])->assertJson(['saved' => false]);
        $this->postJson('/owner/favorites/vet/'.$vet->id, ['saved' => 'wrong'])->assertUnprocessable();
        $this->postJson('/owner/favorites/vet/99999', ['saved' => true])->assertNotFound();
    }

    public function test_adoption_application_cannot_target_inactive_shelter_or_duplicate(): void
    {
        $s = $this->shelter();
        $listing = $this->listing($s);
        $url = '/owner/browse-adoption/'.$listing->id.'/apply';
        $this->post($url, ['message' => 'We have a caring home.'])->assertRedirect('/owner/browse-adoption');
        $this->post($url, ['message' => 'Again'])->assertSessionHas('error');
        $this->assertDatabaseCount('adoption_applications', 1);
        $second = $this->listing($s);
        $s->update(['status' => 'inactive']);
        $this->post('/owner/browse-adoption/'.$second->id.'/apply', ['message' => 'Please'])->assertSessionHas('error');
        $this->get('/owner/browse-adoption/'.$second->id)->assertNotFound();
    }

    public function test_clinic_settings_validate_coordinates_and_save_services(): void
    {
        $vet = $this->vet();
        $this->actingAs($vet);
        $this->patch('/profile', ['name' => $vet->name, 'latitude' => 100, 'longitude' => 10])->assertSessionHasErrors('latitude');
        $this->patch('/profile', ['name' => $vet->name, 'latitude' => 24.86])->assertSessionHasErrors('longitude');
        $this->patch('/profile', ['name' => $vet->name, 'city' => 'Karachi', 'latitude' => 24.86, 'longitude' => 67.01, 'online_consultation' => 1, 'emergency_services' => 1])->assertRedirect('/profile');
        $this->assertTrue($vet->fresh()->vetProfile->online_consultation);
        $this->assertTrue($vet->fresh()->vetProfile->emergency_services);
    }

    public function test_empty_states_render_and_owner_navigation_still_works(): void
    {
        foreach (['/owner/care', '/owner/health-records', '/owner/browse-vets', '/owner/browse-adoption', '/owner/dashboard', '/owner/pets'] as $url) {
            $this->get($url)->assertOk();
        }
    }
}
