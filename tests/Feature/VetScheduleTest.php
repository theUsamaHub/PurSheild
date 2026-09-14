<?php

use App\Http\Middleware\MaintenanceModeMiddleware;
use App\Middleware\RoleMiddleware;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use App\Models\VetAvailability;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class VetScheduleTest extends TestCase
{
    use RefreshDatabase;

    private User $vet;

    private User $owner;

    private Pet $pet;

    protected function migrateFreshUsing(): array
    {
        // Scope this suite to scheduling tables: the unrelated legacy category
        // migration cannot drop an indexed column on SQLite.
        $paths = [
            'database/migrations/0001_01_01_000000_create_users_table.php',
            'database/migrations/2024_01_01_000001_create_roles_and_permissions_tables.php',
            'database/migrations/2026_07_24_175801_create_settings_table.php',
            'database/migrations/2026_07_29_073215_create_notifications_table.php',
            'database/migrations/2026_09_13_000001_add_primary_and_sort_order_to_pet_images_table.php',
        ];
        foreach (glob(database_path('migrations/2026_09_12_*.php')) as $path) {
            if (substr(basename($path), 11, 6) <= '000017') {
                $paths[] = $path;
            }
        }

        return ['--path' => array_map(fn ($path) => str_starts_with($path, 'database/') ? base_path($path) : $path, $paths), '--realpath' => true];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(Carbon::parse('2026-09-14 08:00:00'));
        config(['view.compiled' => sys_get_temp_dir().'/pursheild-vet-test-views', 'session.driver' => 'array']);
        if (! is_dir(sys_get_temp_dir().'/pursheild-vet-test-views')) {
            mkdir(sys_get_temp_dir().'/pursheild-vet-test-views', 0777, true);
        }
        $this->withoutMiddleware(MaintenanceModeMiddleware::class);
        $this->vet = User::factory()->create(['name' => 'Dr. Ahmed Khan']);
        $this->owner = User::factory()->create(['name' => 'Sarah Mitchell']);
        $role = DB::table('roles')->insertGetId(['name' => 'Veterinarian', 'slug' => 'vet']);
        $this->vet->roles()->attach($role);
        $species = DB::table('species')->insertGetId(['name' => 'Dog']);
        $this->pet = Pet::create(['owner_id' => $this->owner->id, 'species_id' => $species, 'name' => 'Buddy']);
        $this->actingAs($this->vet);
    }

    private function appointment(array $overrides = []): Appointment
    {
        return Appointment::create(array_merge([
            'vet_id' => $this->vet->id, 'owner_id' => $this->owner->id, 'pet_id' => $this->pet->id,
            'appointment_date' => '2026-09-14', 'appointment_time' => '10:00:00', 'reason' => 'Annual checkup', 'status' => 'pending',
        ], $overrides));
    }

    private function slot(array $overrides = []): VetAvailability
    {
        return VetAvailability::create(array_merge(['vet_id' => $this->vet->id, 'day_of_week' => 'monday', 'start_time' => '09:00:00', 'end_time' => '17:00:00', 'is_available' => true], $overrides));
    }

    public function test_filters_search_reason_and_keep_other_vets_private(): void
    {
        $this->appointment(['reason' => 'Special allergy consultation']);
        $this->appointment(['reason' => 'Special allergy consultation', 'vet_id' => $this->owner->id]);
        $this->appointment(['reason' => 'Other consultation']);
        $response = $this->get('/vet/appointments?search=allergy&period=week');
        $response->assertOk()->assertViewHas('appointments', fn ($items) => $items->total() === 1);
        $this->get('/vet/appointments?date=not-a-date')->assertSessionHasErrors('date');
    }

    public function test_exact_date_overrides_week_and_pagination_retains_filters(): void
    {
        for ($i = 0; $i < 9; $i++) {
            $this->appointment(['appointment_date' => '2026-10-05', 'reason' => 'Vaccine']);
        }
        $this->get('/vet/appointments?date=2026-10-05&search=Vaccine&status=pending')
            ->assertOk()->assertViewHas('appointments', fn ($items) => $items->total() === 9 && $items->count() === 8 && str_contains($items->nextPageUrl(), 'search=Vaccine'));
    }

    public function test_week_filter_excludes_other_weeks_and_search_accepts_zero(): void
    {
        $this->appointment(['reason' => 'Checkup 0']);
        $this->appointment(['appointment_date' => '2026-10-05']);
        $this->get('/vet/appointments?search=0')->assertOk()->assertViewHas('appointments', fn ($items) => $items->total() === 1);
        $this->get('/vet/appointments')->assertViewHas('appointments', fn ($items) => $items->total() === 1);
    }

    public function test_overlaps_short_slots_and_other_vet_edits_are_rejected(): void
    {
        $slot = $this->slot();
        $data = ['day_of_week' => 'monday', 'start_time' => '10:00', 'end_time' => '11:00', 'is_available' => 1];
        $this->post('/vet/availability', $data)->assertSessionHas('error');
        $this->post('/vet/availability', array_merge($data, ['end_time' => '10:10']))->assertSessionHasErrors('end_time');
        $this->actingAs($this->owner)->withoutMiddleware(RoleMiddleware::class)
            ->put('/vet/availability/'.$slot->id, $data)->assertForbidden();
    }

    public function test_second_slot_can_be_updated_and_deleted_independently(): void
    {
        $first = $this->slot(['end_time' => '12:00:00']);
        $second = $this->slot(['start_time' => '16:00:00', 'end_time' => '19:00:00']);
        $this->put('/vet/availability/'.$second->id, ['day_of_week' => 'monday', 'start_time' => '15:00', 'end_time' => '19:00', 'is_available' => 1])->assertSessionHas('success');
        $this->assertEquals('15:00:00', $second->fresh()->start_time);
        $this->delete('/vet/availability/'.$second->id)->assertSessionHas('success');
        $this->assertNotNull($first->fresh());
        $this->assertNull($second->fresh());
    }

    public function test_copy_keeps_existing_days_and_copies_every_source_slot(): void
    {
        $this->slot(['end_time' => '12:00:00']);
        $this->slot(['start_time' => '16:00:00', 'end_time' => '19:00:00']);
        $tuesday = $this->slot(['day_of_week' => 'tuesday', 'start_time' => '14:00:00']);
        $this->post('/vet/availability/copy-week', ['source_day' => 'monday'])->assertSessionHas('success');
        $this->assertEquals(13, VetAvailability::count());
        $this->assertEquals('14:00:00', $tuesday->fresh()->start_time);
    }

    public function test_reschedule_checks_availability_conflicts_and_terminal_states(): void
    {
        $this->slot();
        $appointment = $this->appointment();
        $other = $this->appointment(['appointment_time' => '11:00:00']);
        $url = '/vet/appointments/'.$appointment->id.'/reschedule';
        $this->put($url, ['appointment_date' => '2026-09-14', 'appointment_time' => '18:00'])->assertSessionHasErrors('appointment_time');
        $this->put($url, ['appointment_date' => '2026-09-14', 'appointment_time' => '11:00'])->assertSessionHasErrors('appointment_time');
        $this->put($url, ['appointment_date' => '2026-09-14', 'appointment_time' => '07:00'])->assertSessionHasErrors('appointment_time');
        $this->put($url, ['appointment_date' => '2026-09-14', 'appointment_time' => '12:00'])->assertSessionHas('success');
        $this->assertEquals('rescheduled', $appointment->fresh()->status);
        $this->assertEquals('12:00:00', $appointment->fresh()->appointment_time);
        $appointment->update(['status' => 'completed']);
        $this->put($url, ['appointment_date' => '2026-09-14', 'appointment_time' => '13:00'])->assertSessionHasErrors('appointment_date');
        $this->assertEquals('11:00:00', $other->fresh()->appointment_time);
    }

    public function test_foreign_reschedule_is_forbidden_and_treatment_required_to_complete(): void
    {
        $foreign = $this->appointment(['vet_id' => $this->owner->id]);
        $this->put('/vet/appointments/'.$foreign->id.'/reschedule', ['appointment_date' => '2026-09-14', 'appointment_time' => '12:00'])->assertForbidden();
        $appointment = $this->appointment(['status' => 'rescheduled']);
        $this->put('/vet/appointments/'.$appointment->id.'/status', ['status' => 'completed'])->assertSessionHas('error');
        $this->get('/vet/treatments/'.$appointment->id.'/create')->assertOk();
    }

    public function test_uploaded_pet_photos_and_primary_gallery_image_use_storage_urls(): void
    {
        $this->pet->update(['profile_image' => 'uploads/pets/buddy.jpg']);
        $this->appointment();
        $this->get('/vet/appointments')->assertOk()->assertSee('/storage/uploads/pets/buddy.jpg', false);
        $this->pet->images()->create(['image_path' => 'uploads/pets/primary.jpg', 'is_primary' => true, 'sort_order' => 0]);
        $this->get('/vet/appointments')->assertOk()->assertSee('/storage/uploads/pets/primary.jpg', false)->assertDontSee('/storage/uploads/pets/buddy.jpg', false);
    }

    public function test_pages_render_with_multiple_slots_photos_and_all_statuses(): void
    {
        $this->slot(['start_time' => '10:00:00', 'end_time' => '12:00:00']);
        $this->slot(['start_time' => '16:00:00', 'end_time' => '19:00:00']);
        foreach (['tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day) {
            $this->slot(['day_of_week' => $day]);
        }
        $this->slot(['day_of_week' => 'sunday', 'is_available' => false]);
        $availability = $this->get('/vet/availability')->assertOk()->assertSee('4:00 PM')->assertSee('Copy Week Schedule');

        $names = ['Buddy', 'Luna', 'Max', 'Milo', 'Bella', 'Charlie', 'Daisy', 'Simba'];
        $statuses = ['completed', 'pending', 'approved', 'completed', 'rescheduled', 'approved', 'pending', 'cancelled'];
        foreach ($names as $i => $name) {
            $pet = Pet::create(['owner_id' => $this->owner->id, 'species_id' => $this->pet->species_id, 'name' => $name]);
            $apt = $this->appointment(['pet_id' => $pet->id, 'appointment_date' => '2026-09-'.(14 + min($i, 6)), 'status' => $statuses[$i]]);
        }
        $appointments = $this->get('/vet/appointments')->assertOk()->assertSee('Rescheduled')->assertSee('Showing 8 of 8 appointments');

        $this->get('/vet/appointments/'.$apt->id)->assertOk();
    }
}
