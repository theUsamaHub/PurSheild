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
            'database/migrations/2026_09_12_000025_create_furshield_notifications_table.php',
            'database/migrations/2026_09_12_000026_create_reviews_table.php',
            'database/migrations/2026_09_14_000001_add_reply_to_reviews_table.php',
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
        for ($i = 0; $i < 11; $i++) {
            $this->appointment(['appointment_date' => '2026-10-05', 'reason' => 'Vaccine']);
        }
        $this->get('/vet/appointments?date=2026-10-05&search=Vaccine&status=pending')
            ->assertOk()->assertViewHas('appointments', fn ($items) => $items->total() === 11 && $items->count() === 10 && str_contains($items->nextPageUrl(), 'search=Vaccine'));
    }

    public function test_week_filter_excludes_other_weeks_and_search_accepts_zero(): void
    {
        $this->appointment(['reason' => 'Checkup 0']);
        $this->appointment(['appointment_date' => '2026-10-05']);
        $this->get('/vet/appointments?search=0')->assertOk()->assertViewHas('appointments', fn ($items) => $items->total() === 1);
        $this->get('/vet/appointments?period=week')->assertViewHas('appointments', fn ($items) => $items->total() === 1);
        $this->get('/vet/appointments')->assertViewHas('appointments', fn ($items) => $items->total() === 2);
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

    private function review(array $overrides = []): \App\Models\Review
    {
        return \App\Models\Review::create(array_merge([
            'user_id' => $this->owner->id, 'reviewable_type' => User::class,
            'reviewable_id' => $this->vet->id, 'rating' => 5, 'comment' => 'Excellent care',
        ], $overrides));
    }

    public function test_all_vet_navigation_destinations_render(): void
    {
        $this->appointment();
        $this->review();
        foreach (['dashboard', 'appointments', 'availability', 'patients', 'treatments', 'reviews', 'notifications'] as $page) {
            $this->get('/vet/'.$page)->assertOk();
        }
        $this->get('/profile')->assertOk();
        $this->get('/vet/patients/'.$this->pet->id)->assertOk()->assertSee('View appointment');
    }

    public function test_review_filters_combine_and_paginate_without_leaking_other_vets(): void
    {
        for ($i = 0; $i < 16; $i++) {
            $this->review(['rating' => 4, 'comment' => 'Allergy followup '.$i]);
        }
        $this->review(['rating' => 1]);
        $this->review(['rating' => 4, 'comment' => 'Allergy private', 'reviewable_id' => $this->owner->id]);
        $this->get('/vet/reviews?search=allergy&rating=4&sort=oldest&response=unanswered')
            ->assertOk()->assertViewHas('reviews', fn ($items) => $items->total() === 16
                && $items->count() === 15 && str_contains($items->nextPageUrl(), 'rating=4'));
        $this->get('/vet/reviews?search=0')->assertViewHas('reviews', fn ($items) => $items->total() === 2);
        $this->get('/vet/reviews?search=Sarah')->assertViewHas('reviews', fn ($items) => $items->total() === 17);
        $this->get('/vet/reviews?search[]=bad')->assertSessionHasErrors('search');
        $this->get('/vet/reviews?rating=6')->assertSessionHasErrors('rating');
        $this->get('/vet/reviews?sort=invalid')->assertSessionHasErrors('sort');
    }

    public function test_review_sort_orders_and_response_filters(): void
    {
        $old = $this->review(['rating' => 2]);
        $old->forceFill(['created_at' => now()->subDays(2)])->save();
        $new = $this->review(['rating' => 5, 'created_at' => now()]);
        foreach (['oldest' => $old, 'lowest' => $old, 'latest' => $new, 'highest' => $new] as $sort => $first) {
            $this->get('/vet/reviews?sort='.$sort)->assertOk()
                ->assertViewHas('reviews', fn ($items) => $items->first()->id === $first->id);
        }
        $this->post('/vet/reviews/'.$old->id.'/reply', ['reply' => 'Thank you for the feedback.'])->assertSessionHas('success');
        $this->assertNotNull($old->fresh()->replied_at);
        $this->get('/vet/reviews?response=replied&search=feedback')->assertViewHas('reviews', fn ($items) => $items->total() === 1);
        $this->get('/vet/reviews?response=unanswered')->assertViewHas('reviews', fn ($items) => $items->total() === 1 && $items->first()->id === $new->id);
        $this->post('/vet/reviews/'.$new->id.'/reply', ['reply' => '   '])->assertSessionHasErrors('reply');
        $foreign = $this->review(['reviewable_id' => $this->owner->id]);
        $this->post('/vet/reviews/'.$foreign->id.'/reply', ['reply' => 'Changed'])->assertForbidden();
        $this->assertNull($foreign->fresh()->reply);
    }

    public function test_appointment_periods_status_and_exact_date_are_consistent(): void
    {
        $this->appointment(['status' => 'completed']);
        $future = $this->appointment(['appointment_date' => '2026-10-05', 'status' => 'rescheduled']);
        $this->appointment(['appointment_time' => '07:00:00', 'status' => 'approved']);
        $this->appointment(['appointment_date' => '2026-09-15', 'status' => 'cancelled']);
        $this->get('/vet/appointments?period=upcoming')->assertViewHas('appointments', fn ($items) => $items->total() === 1 && $items->first()->id === $future->id);
        $this->get('/vet/appointments?period=today')->assertViewHas('appointments', fn ($items) => $items->total() === 2);
        $this->get('/vet/appointments?period=month')->assertViewHas('appointments', fn ($items) => $items->total() === 3);
        $this->get('/vet/appointments?period=week&date=2026-10-05&status=rescheduled')->assertViewHas('appointments', fn ($items) => $items->total() === 1);
    }

    public function test_dashboard_counts_all_upcoming_including_rescheduled(): void
    {
        for ($i = 0; $i < 7; $i++) {
            $this->appointment(['status' => 'rescheduled', 'appointment_date' => '2026-09-15']);
        }
        $this->appointment(['status' => 'cancelled', 'appointment_date' => '2026-09-15']);
        $this->appointment(['status' => 'approved', 'appointment_time' => '07:00:00']);
        $this->get('/vet/dashboard')->assertOk()->assertViewHas('upcomingCount', 7);
    }

    public function test_patient_last_visit_uses_completed_visits_and_validates_filters(): void
    {
        $this->appointment(['status' => 'completed', 'appointment_date' => '2026-09-01']);
        $this->appointment(['status' => 'approved', 'appointment_date' => '2026-10-05']);
        $this->get('/vet/patients?search=Buddy&species_id='.$this->pet->species_id)
            ->assertOk()->assertViewHas('patients', fn ($items) => $items->total() === 1 && Carbon::parse($items->first()->last_visit_date)->toDateString() === '2026-09-01');
        $this->get('/vet/patients?search[]=bad')->assertSessionHasErrors('search');
        $foreignPet = Pet::create(['owner_id' => $this->owner->id, 'species_id' => $this->pet->species_id, 'name' => 'Private']);
        $this->get('/vet/patients/'.$foreignPet->id)->assertForbidden();
    }

    public function test_treatment_submission_records_prescriptions_and_completes_once(): void
    {
        $appointment = $this->appointment(['status' => 'approved']);
        $data = ['symptoms' => 'Itching', 'diagnosis' => 'Allergy', 'treatment' => 'Skin care',
            'prescriptions' => [['medicine_name' => 'Test medicine', 'dosage' => 'Test dosage', 'frequency' => 'Daily', 'duration' => '3 days']]];
        $this->post('/vet/treatments/'.$appointment->id, $data)->assertSessionHas('success');
        $this->assertEquals('completed', $appointment->fresh()->status);
        $treatment = $appointment->fresh()->treatment;
        $this->assertCount(1, $treatment->prescriptions);
        $this->get('/vet/treatments/'.$treatment->id)->assertOk()->assertSee('Test medicine');
        $this->get('/vet/treatments/'.$appointment->id.'/create')->assertRedirect(route('vet.treatments.show', $treatment));
        $this->post('/vet/treatments/'.$appointment->id, $data)->assertStatus(400);
        $this->assertEquals(1, $appointment->treatment()->count());
        $this->get('/vet/treatments?search=Allergy&date=2026-09-14')->assertViewHas('treatments', fn ($items) => $items->total() === 1);
        $this->get('/vet/treatments?date=bad')->assertSessionHasErrors('date');
        $this->actingAs($this->owner)->withoutMiddleware(RoleMiddleware::class)->get('/vet/treatments/'.$treatment->id)->assertForbidden();
    }

    public function test_invalid_prescription_preserves_input_and_writes_nothing(): void
    {
        $appointment = $this->appointment(['status' => 'rescheduled']);
        $this->from('/vet/treatments/'.$appointment->id.'/create')->post('/vet/treatments/'.$appointment->id, [
            'symptoms' => 'Itching', 'diagnosis' => 'Allergy', 'treatment' => 'Skin care',
            'prescriptions' => [['medicine_name' => 'Keep this input', 'dosage' => '', 'frequency' => 'Daily', 'duration' => '3 days']],
        ])->assertSessionHasErrors('prescriptions.0.dosage')->assertSessionHasInput('prescriptions.0.medicine_name', 'Keep this input');
        $this->assertNull($appointment->fresh()->treatment);
        $this->assertEquals('rescheduled', $appointment->fresh()->status);
        $this->get('/vet/treatments/'.$appointment->id.'/create')->assertOk()->assertSee('Keep this input');
    }

    public function test_notifications_merge_sources_filter_and_scope_read_actions(): void
    {
        $practice = \App\Models\FurshieldNotification::create(['user_id' => $this->vet->id, 'title' => 'Practice notice', 'message' => 'New appointment', 'type' => 'appointment', 'link' => '/vet/appointments', 'is_read' => false]);
        $foreign = \App\Models\FurshieldNotification::create(['user_id' => $this->owner->id, 'title' => 'Private notice', 'message' => 'Private', 'type' => 'appointment', 'is_read' => false]);
        $system = $this->vet->notifications()->create(['id' => (string) \Illuminate\Support\Str::uuid(), 'type' => 'test', 'data' => ['title' => 'System notice', 'message' => 'Account update', 'link' => 'javascript:alert(1)']]);
        $this->get('/vet/notifications')->assertOk()->assertSee('Practice notice')->assertSee('System notice')->assertDontSee('Private notice')->assertDontSee('javascript:alert(1)', false)
            ->assertViewHas('notifications', fn ($items) => $items->total() === 2);
        $this->get('/vet/dashboard')->assertOk()->assertSee('Practice notice')->assertSee('System notice');
        $this->assertEquals(2, \App\Support\VetNotifications::unreadCount($this->vet));
        $this->patch('/vet/notifications/practice/'.$foreign->id.'/read')->assertNotFound();
        $this->patch('/vet/notifications/practice/'.$practice->id.'/read')->assertSessionHas('success');
        $this->get('/vet/notifications?status=unread')->assertViewHas('notifications', fn ($items) => $items->total() === 1);
        $this->patch('/vet/notifications/system/'.$system->id.'/read')->assertSessionHas('success');
        $this->assertEquals(0, \App\Support\VetNotifications::unreadCount($this->vet));
        $practice->update(['is_read' => false]);
        $this->patch('/vet/notifications/read-all')->assertSessionHas('success');
        $this->assertTrue($practice->fresh()->is_read);
        $this->assertFalse($foreign->fresh()->is_read);
        $this->get('/vet/notifications?status=read')->assertViewHas('notifications', fn ($items) => $items->total() === 2);
    }

    public function test_prescription_failure_rolls_back_treatment_and_appointment(): void
    {
        $appointment = $this->appointment(['status' => 'approved']);
        $event = 'eloquent.creating: App\\Models\\Prescription';
        \Illuminate\Support\Facades\Event::listen($event, function () {
            throw new \RuntimeException('Simulated prescription write failure');
        });
        $this->withoutExceptionHandling();
        try {
            $this->post('/vet/treatments/'.$appointment->id, [
                'symptoms' => 'Test symptoms', 'diagnosis' => 'Test diagnosis', 'treatment' => 'Test treatment',
                'prescriptions' => [['medicine_name' => 'Test', 'dosage' => 'Test', 'frequency' => 'Test', 'duration' => 'Test']],
            ]);
            $this->fail('The simulated write failure should propagate.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Simulated prescription write failure', $exception->getMessage());
        } finally {
            \Illuminate\Support\Facades\Event::forget($event);
        }
        $this->assertNull($appointment->fresh()->treatment);
        $this->assertEquals('approved', $appointment->fresh()->status);
    }

    public function test_document_download_requires_a_patient_relationship_and_matching_pet(): void
    {
        $this->appointment();
        $folder = sys_get_temp_dir().'/pursheild-doc-test-'.\Illuminate\Support\Str::uuid();
        config(['filesystems.disks.public.root' => $folder]);
        \Illuminate\Support\Facades\Storage::forgetDisk('public');
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        $disk->put('report.pdf', 'Test document contents');
        $document = $this->pet->medicalDocuments()->create(['file_name' => 'report.pdf', 'file_path' => 'report.pdf']);
        try {
            $url = '/vet/patients/'.$this->pet->id.'/documents/'.$document->id;
            $this->get($url)->assertOk()->assertDownload('report.pdf');
            $this->get('/vet/patients/'.$this->pet->id)->assertOk()->assertSee($url, false);
            $otherPet = Pet::create(['owner_id' => $this->owner->id, 'species_id' => $this->pet->species_id, 'name' => 'Other']);
            $this->get('/vet/patients/'.$otherPet->id.'/documents/'.$document->id)->assertNotFound();
            $this->actingAs($this->owner)->withoutMiddleware(RoleMiddleware::class)->get($url)->assertForbidden();
            $this->actingAs($this->vet);
            $disk->delete('report.pdf');
            $this->get($url)->assertNotFound();
        } finally {
            $disk->delete('report.pdf');
            if (is_dir($folder)) {
                rmdir($folder);
            }
        }
    }
}
