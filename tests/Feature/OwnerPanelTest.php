<?php

use App\Http\Middleware\MaintenanceModeMiddleware;
use App\Models\Appointment;
use App\Models\Breed;
use App\Models\CareContent;
use App\Models\Cart;
use App\Models\FurshieldNotification;
use App\Models\HealthRecord;
use App\Models\Pet;
use App\Models\Product;
use App\Models\Species;
use App\Models\User;
use App\Models\Vaccination;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OwnerPanelTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;

    private User $other;

    private Species $dog;

    private Breed $breed;

    protected function migrateFreshUsing(): array
    {
        $names = ['0001_01_01_000000_create_users_table.php', '2024_01_01_000001_create_roles_and_permissions_tables.php', '2026_07_24_175801_create_settings_table.php', '2026_07_29_073215_create_notifications_table.php', '2026_09_13_000001_add_primary_and_sort_order_to_pet_images_table.php', '2026_09_14_000001_add_reply_to_reviews_table.php'];
        foreach (glob(database_path('migrations/2026_09_12_*.php')) as $file) {
            $names[] = basename($file);
        }

        return ['--path' => array_map(fn ($name) => database_path('migrations/'.$name), $names), '--realpath' => true];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo('2026-09-15 12:00:00');
        $views = sys_get_temp_dir().'/pursheild-owner-test-views';
        if (! is_dir($views)) {
            mkdir($views, 0777, true);
        }
        config(['view.compiled' => $views, 'session.driver' => 'array']);
        $this->withoutMiddleware(MaintenanceModeMiddleware::class);
        $this->owner = User::factory()->create(['name' => 'Ayesha Khan', 'status' => 'active']);
        $this->other = User::factory()->create(['name' => 'Other Owner', 'status' => 'active']);
        $role = DB::table('roles')->insertGetId(['name' => 'Pet Owner', 'slug' => 'owner']);
        $this->owner->roles()->attach($role);
        $this->dog = Species::create(['name' => 'Dog', 'status' => 'active']);
        $this->breed = Breed::create(['name' => 'Golden Retriever', 'species_id' => $this->dog->id]);
        $this->actingAs($this->owner);
    }

    private function pet(array $overrides = []): Pet
    {
        return Pet::create(array_merge(['owner_id' => $this->owner->id, 'name' => 'Bruno', 'species_id' => $this->dog->id, 'breed_id' => $this->breed->id, 'gender' => 'male', 'date_of_birth' => '2024-03-14', 'weight' => 28], $overrides));
    }

    private function data(array $overrides = []): array
    {
        return array_merge(['name' => 'Coco', 'species_id' => $this->dog->id, 'breed_id' => $this->breed->id, 'gender' => 'female', 'date_of_birth' => '2025-09-10', 'weight' => 2], $overrides);
    }

    private function vaccine(Pet $pet, array $overrides = []): Vaccination
    {
        return Vaccination::create(array_merge(['pet_id' => $pet->id, 'vaccine_name' => 'Rabies', 'vaccination_date' => '2025-09-16', 'next_due_date' => '2026-09-16'], $overrides));
    }

    private function product(array $overrides = []): Product
    {
        $category = DB::table('categories')->insertGetId(['name' => 'Food', 'slug' => 'food-'.uniqid()]);

        return Product::create(array_merge(['category_id' => $category, 'name' => 'Bruno food', 'slug' => 'food-'.uniqid(), 'price' => 25, 'stock_quantity' => 5, 'status' => 'active'], $overrides));
    }

    public function test_both_pages_render_the_shell_photos_and_linked_add_form(): void
    {
        $pet = $this->pet(['profile_image' => 'uploads/pets/bruno.png']);
        $this->vaccine($pet);
        $this->get('/owner/dashboard')->assertOk()->assertSee('Welcome back, Ayesha!')->assertSee('Upcoming Reminders')->assertSee('Pet Owner Panel');
        $page = $this->get('/owner/pets')->assertOk()->assertSee('Add New Pet')->assertSee('Vaccination Due')->assertSee('uploads/pets/bruno.png')->assertSee('newPetSpecies');
        $this->get('/owner/pets/create')->assertRedirect('/owner/pets?panel=add');
        $this->get('/owner/pets?panel=closed')->assertOk()->assertDontSee('id="ownerAddPetForm"', false);
        $this->get('/owner/pets?view=health')->assertOk()->assertSee(route('owner.health.index', $pet));
        foreach (['/owner/pets/'.$pet->id, '/owner/pets/'.$pet->id.'/edit', '/owner/pets/'.$pet->id.'/health', '/profile'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_pet_search_species_status_sort_and_pagination_combine_without_leaking(): void
    {
        for ($i = 0; $i < 8; $i++) {
            $this->vaccine($this->pet(['name' => 'Bruno '.$i]));
        }
        $this->pet(['name' => 'Healthy']);
        $this->pet(['name' => 'Private Bruno', 'owner_id' => $this->other->id]);
        $url = '/owner/pets?search=Bruno&species_id='.$this->dog->id.'&status=vaccination_due&sort=name';
        $this->get($url)->assertOk()->assertViewHas('pets', fn ($items) => $items->total() === 8 && $items->count() === 6 && str_contains($items->nextPageUrl(), 'status=vaccination_due'))->assertDontSee('Private Bruno');
        $this->get('/owner/pets?status=healthy')->assertViewHas('pets', fn ($items) => $items->total() === 1);
        $this->get('/owner/pets?search=0')->assertViewHas('pets', fn ($items) => $items->total() === 1);
        $this->get('/owner/pets?search[]=wrong')->assertSessionHasErrors('search');
        $this->get('/owner/pets?status=wrong&sort=wrong')->assertSessionHasErrors(['status', 'sort']);
    }

    public function test_newer_vaccination_replaces_old_due_date_in_cards_and_dashboard(): void
    {
        $pet = $this->pet();
        $this->vaccine($pet);
        $this->vaccine($pet, ['vaccination_date' => '2026-09-14', 'next_due_date' => '2027-09-14']);
        $this->get('/owner/pets?status=vaccination_due')->assertViewHas('pets', fn ($items) => $items->total() === 0);
        $this->get('/owner/dashboard')->assertViewHas('stats', fn ($stats) => $stats['vaccinations'] === 0);
        $this->vaccine($pet, ['vaccine_name' => 'Distemper', 'next_due_date' => '2026-09-10']);
        $this->get('/owner/dashboard')->assertViewHas('stats', fn ($stats) => $stats['vaccinations'] === 1)->assertSee('Overdue');
    }

    public function test_upcoming_counts_exclude_past_and_terminal_visits_and_sort_soonest_first(): void
    {
        $pet = $this->pet();
        foreach ([['2026-09-16', 'approved'], ['2026-09-17', 'rescheduled'], ['2026-09-14', 'pending'], ['2026-09-18', 'completed'], ['2026-09-18', 'rejected'], ['2026-09-18', 'cancelled'], ['2026-09-15', 'pending']] as [$date,$status]) {
            Appointment::create(['owner_id' => $this->owner->id, 'pet_id' => $pet->id, 'vet_id' => $this->other->id, 'appointment_date' => $date, 'appointment_time' => '10:00', 'status' => $status]);
        }
        $private = $this->pet(['owner_id' => $this->other->id]);
        Appointment::create(['owner_id' => $this->other->id, 'pet_id' => $private->id, 'vet_id' => $this->owner->id, 'appointment_date' => '2026-09-16', 'appointment_time' => '10:00', 'status' => 'approved']);
        $this->get('/owner/dashboard')->assertViewHas('stats', fn ($s) => $s['upcoming'] === 2 && $s['pets'] === 1)->assertViewHas('appointments', fn ($items) => $items->count() === 2 && $items->first()->appointment_date->toDateString() === '2026-09-16');
    }

    public function test_dashboard_cart_and_health_counts_and_search_use_visible_owned_records(): void
    {
        $pet = $this->pet();
        HealthRecord::create(['pet_id' => $pet->id, 'record_date' => '2026-09-14', 'description' => 'Checkup']);
        $product = $this->product();
        $cart = Cart::create(['owner_id' => $this->owner->id]);
        $cart->items()->create(['product_id' => $product->id, 'quantity' => 4, 'price' => 25]);
        $this->pet(['name' => 'Bruno private', 'owner_id' => $this->other->id]);
        $this->product(['name' => 'Bruno hidden', 'status' => 'inactive']);
        CareContent::create(['title' => 'Bruno training', 'category' => 'training', 'content_type' => 'article', 'status' => 'active']);
        $this->get('/owner/dashboard?search=Bruno')->assertOk()->assertViewHas('stats', fn ($s) => $s['cart_items'] === 4 && $s['health_records'] === 1)->assertViewHas('searchResults', fn ($items) => $items->count() === 3)->assertDontSee('Bruno private')->assertDontSee('Bruno hidden');
        $this->get('/owner/dashboard?search[]=wrong')->assertSessionHasErrors('search');
    }

    public function test_pet_save_validates_breed_dates_weight_and_preserves_existing_optional_fields(): void
    {
        $cat = Species::create(['name' => 'Cat']);
        $breed = Breed::create(['name' => 'Persian', 'species_id' => $cat->id]);
        $this->post('/owner/pets', $this->data(['breed_id' => $breed->id]))->assertSessionHasErrors('breed_id');
        $this->post('/owner/pets', $this->data(['date_of_birth' => '2026-09-16', 'weight' => -1]))->assertSessionHasErrors(['date_of_birth', 'weight']);
        $this->post('/owner/pets', $this->data(['description' => str_repeat('a', 5001)]))->assertSessionHasErrors('description');
        $this->post('/owner/pets', $this->data(['date_of_birth' => '2026-09-15']))->assertSessionHas('success');
        $pet = Pet::firstOrFail();
        $this->assertSame($this->owner->id, $pet->owner_id);
        $pet->update(['is_neutered' => true, 'microchip_number' => 'CHIP123']);
        $this->put('/owner/pets/'.$pet->id, $this->data(['name' => 'Coco updated']))->assertSessionHas('success');
        $this->assertTrue($pet->fresh()->is_neutered);
        $this->assertSame('CHIP123', $pet->fresh()->microchip_number);
    }

    public function test_foreign_pet_actions_and_foreign_gallery_ids_are_rejected(): void
    {
        $pet = $this->pet();
        $foreign = $this->pet(['owner_id' => $this->other->id]);
        $image = $foreign->images()->create(['image_path' => 'private.jpg']);
        $this->get('/owner/pets/'.$foreign->id)->assertForbidden();
        $this->get('/owner/pets/'.$foreign->id.'/edit')->assertForbidden();
        $this->put('/owner/pets/'.$foreign->id, $this->data())->assertForbidden();
        $this->delete('/owner/pets/'.$foreign->id)->assertForbidden();
        $this->put('/owner/pets/'.$pet->id, $this->data(['remove_images' => [$image->id], 'primary_image_id' => $image->id]))->assertSessionHasErrors(['remove_images.0', 'primary_image_id']);
    }

    public function test_photo_upload_limits_and_gallery_replacement(): void
    {
        Storage::fake('public');
        $pixel = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aDUcAAAAASUVORK5CYII=');
        $this->post('/owner/pets', $this->data(['profile_image' => UploadedFile::fake()->create('bad.txt', 1)]))->assertSessionHasErrors('profile_image');
        $this->post('/owner/pets', $this->data(['profile_image' => UploadedFile::fake()->createWithContent('pet.png', $pixel)]))->assertSessionHas('success');
        $pet = Pet::firstOrFail();
        Storage::disk('public')->assertExists($pet->profile_image);
        for ($i = 0; $i < 6; $i++) {
            $pet->images()->create(['image_path' => 'gallery-'.$i.'.png', 'sort_order' => $i, 'is_primary' => $i === 0]);
        }
        $this->put('/owner/pets/'.$pet->id, $this->data(['images' => [UploadedFile::fake()->createWithContent('extra.png', $pixel)]]))->assertSessionHasErrors('images');
        $old = $pet->images()->first();
        $this->put('/owner/pets/'.$pet->id, $this->data(['remove_images' => [$old->id], 'primary_image_id' => $old->id]))->assertSessionHasErrors('primary_image_id');
        $this->put('/owner/pets/'.$pet->id, $this->data(['remove_images' => [$old->id], 'images' => [UploadedFile::fake()->createWithContent('replace.png', $pixel)]]))->assertSessionHas('success');
        $this->assertSame(6, $pet->images()->count());
        $this->assertSame(1, $pet->images()->where('is_primary', true)->count());
    }

    public function test_delete_cleans_profile_photo_and_preserves_medical_history(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('uploads/pets/test.png', 'test');
        $pet = $this->pet(['profile_image' => 'uploads/pets/test.png']);
        $this->delete('/owner/pets/'.$pet->id)->assertSessionHas('success');
        Storage::disk('public')->assertMissing('uploads/pets/test.png');
        $kept = $this->pet();
        $record = HealthRecord::create(['pet_id' => $kept->id, 'record_date' => '2026-09-14']);
        $this->delete('/owner/pets/'.$kept->id)->assertSessionHasErrors('pet');
        $this->assertNotNull($kept->fresh());
        $this->assertNotNull($record->fresh());
    }

    public function test_notification_panel_and_read_actions_are_scoped(): void
    {
        $notice = FurshieldNotification::create(['user_id' => $this->owner->id, 'title' => 'Your appointment', 'message' => 'Visit confirmed', 'type' => 'appointment', 'is_read' => false]);
        $foreign = FurshieldNotification::create(['user_id' => $this->other->id, 'title' => 'Private notice', 'message' => 'Private', 'type' => 'appointment', 'is_read' => false]);
        $this->get('/owner/dashboard?panel=notifications')->assertOk()->assertSee('Visit confirmed')->assertDontSee('Private notice');
        $this->patch('/owner/notifications/practice/'.$foreign->id.'/read')->assertNotFound();
        $this->patch('/owner/notifications/practice/'.$notice->id.'/read')->assertSessionHas('success');
        $this->get('/owner/dashboard?panel=notifications&notice_status=unread')->assertViewHas('notifications', fn ($items) => $items->total() === 0);
        $this->patch('/owner/notifications/read-all')->assertSessionHas('success');
        $this->assertFalse($foreign->fresh()->is_read);
    }
}
