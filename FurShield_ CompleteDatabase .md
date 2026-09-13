```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ============================================================================
 * FurShield — Complete Database Schema
 * ============================================================================
 *
 * TechWiz 6 — Global AI-Based Tech Competition | Aptech Limited
 * Stack: Laravel 13 + PostgreSQL
 *
 * DESIGN PRINCIPLES:
 * ─────────────────
 * 1. Single `users` table for ALL roles (admin, owner, vet, shelter).
 *    WHY: Laravel Breeze/Sanctum auth scaffolding is already built around one
 *    users table. Reusing it avoids duplicating login/password-reset/session
 *    logic four times. Role-specific data lives in extension tables (1:1).
 *
 * 2. Roles use a `role_user` pivot (many-to-many), NOT an enum on users.
 *    WHY: The starter kit already implements this pattern with `HasRoles` trait,
 *    `RoleMiddleware`, and `role:admin` middleware. A user can theoretically
 *    hold multiple roles (e.g., a vet who is also an admin). This is the
 *    existing convention — we keep it.
 *
 * 3. Verification gating via `status` on users + `is_verified` on profiles.
 *    WHY: SRS states shelters need "a verified account" and vets should be
 *    "listed" — someone must approve them. Admin performs verification.
 *    Vets/Shelters start as `pending` → can log in and complete profile →
 *    cannot appear in public listings until Admin sets `status = active`.
 *
 * 4. Soft deletes only where data loss is dangerous (categories).
 *    Hard deletes elsewhere to keep the schema clean for a competition build.
 *
 * 5. Polymorphic relationships for reviews and media (reusable across models).
 *    WHY: Reviews target vets OR adoption listings OR products. Media (images)
 *    attach to pets, products, adoption listings, or user profiles. Polymorphic
 *    avoids creating separate join tables for each combination.
 *
 * TABLE COUNT: 31 tables
 * SRS COMPLIANCE: Full coverage of all functional requirements
 * ============================================================================
 */

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. USERS
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "Pet Owner Registration & Login" — name, contact, email, address.
        | SRS Reference: "Veterinarian Registration" — name, contact, email, address.
        | SRS Reference: "Animal Shelter Registration" — shelter name, contact, email.
        | SRS Reference: "Admin" — our addition for system management.
        |
        | DESIGN DECISION:
        | - Single users table for ALL 4 roles. Laravel Breeze auth scaffolding
        |   (login, register, password reset, email verification, sessions) is
        |   already built around this table. We extend it with role-specific fields
        |   rather than creating separate auth tables.
        | - `role` enum kept for quick checks (e.g., redirect after login).
        |   The `role_user` pivot table provides the full RBAC relationship.
        | - `status` enum handles verification gating: vets/shelters start as
        |   `pending_verification` and become `active` after Admin approval.
        | - `profile_image` stored on users table (not in vet/shelter profiles)
        |   because all roles need a profile photo — it's shared, not role-specific.
        |
        | FIELDS:
        | - phone: SRS requires contact number for all roles.
        | - address: SRS requires address for all roles.
        | - status: Verification gating (SRS: "verified account" for shelters).
        | - suspended_at + suspension_reason: Admin audit trail for disabled accounts.
        | - last_login_at: Useful for session management and "last seen" display.
        | - profile_image: Shared across all roles — photo upload via Breeze profile.
        */

        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('profile_image')->nullable();

            // Quick role check for post-login redirect (admin → /admin/dashboard, etc.)
            // Full RBAC via role_user pivot table.
            $table->enum('role', [
                'admin',
                'owner',
                'vet',
                'shelter'
            ])->default('owner');

            // Verification gating: pending → active (after admin approval)
            // SRS: "Shelters need a verified account" — admin performs verification.
            $table->enum('status', [
                'active',
                'inactive',
                'suspended',
                'pending_verification'
            ])->default('active');

            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();

            // Admin audit fields
            $table->timestamp('suspended_at')->nullable();
            $table->text('suspension_reason')->nullable();
            $table->timestamp('last_login_at')->nullable();

            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | 2. ROLES
        |--------------------------------------------------------------------------
        |
        | SRS Reference: 4 roles defined — Admin (our addition), Pet Owner,
        | Veterinarian, Animal Shelter.
        |
        | DESIGN DECISION:
        | - The starter kit already has this table with a `role_user` pivot.
        | - We keep the existing pattern: roles table + pivot, NOT an enum-only
        |   approach. This allows the `HasRoles` trait, `RoleMiddleware`, and
        |   `role:admin` middleware to work without modification.
        | - `slug` used for middleware checks: `role:admin`, `role:vet`, etc.
        | - `permissions` JSON column for granular per-role permissions (optional,
        |   kept from starter kit for future extensibility).
        */

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('permissions')->nullable();
            $table->timestamps();
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->primary(['user_id', 'role_id']);
        });


        /*
        |--------------------------------------------------------------------------
        | 3. VET PROFILES
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "Veterinarian — Post-login: add specialization, experience,
        | available time slots (extension profile)."
        | SRS Reference: "Admin-verified before going live."
        |
        | DESIGN DECISION:
        | - 1:1 relationship with users (user_id is unique).
        | - Only populated when user's role = 'vet'. Empty for other roles.
        | - `is_verified` is the SRS-required verification flag. Admin sets this
        |   to true after reviewing the vet's profile. Until then, the vet cannot
        |   appear in public listings or accept bookings.
        | - `consultation_fee` displayed to owners during appointment booking.
        |   SRS says "no payment" but owners need to see fees before booking.
        | - `profile_image` kept on users table (shared), not here.
        */

        Schema::create('vet_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('qualification')->nullable();  // e.g., "BVSc, MVSc"
            $table->unsignedInteger('experience_years')->default(0);

            $table->string('clinic_name')->nullable();
            $table->text('clinic_address')->nullable();

            $table->text('bio')->nullable();

            // SRS: owners need to see fees before booking (no payment, but display only)
            $table->decimal('consultation_fee', 8, 2)->nullable();

            // SRS: "Admin-verified before going live" — our addition to close SRS gap
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | 4. SHELTER PROFILES
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "Animal Shelter — Register with shelter name, contact
        | person, email, contact number, address."
        | SRS Reference: "Verified login — SRS explicitly requires a verified
        | account; Admin performs verification."
        |
        | DESIGN DECISION:
        | - 1:1 relationship with users (user_id is unique).
        | - Only populated when user's role = 'shelter'.
        | - `is_verified` — same verification gating as vets. Until admin approves,
        |   shelter cannot appear in public listings or create adoption listings.
        | - `latitude`/`longitude` — SRS: "auto-suggest vets based on location."
        |   Same applies to shelters for map-based search.
        | - `capacity` — dashboard stat: how many animals the shelter can hold.
        | - `profile_image` kept on users table (shared), not here.
        */

        Schema::create('shelter_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('shelter_name');
            $table->text('description')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('contact_number', 30)->nullable();

            $table->string('website')->nullable();

            // SRS: "Verified login" — admin must approve before shelter goes live
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();

            // Dashboard stat + capacity check before creating listings
            $table->unsignedInteger('capacity')->nullable();

            // Location-based search (SRS: "auto-suggest based on location")
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | 5. SPECIES
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "Manage Pet Profiles — name, species, breed, age."
        |
        | DESIGN DECISION:
        | - Admin-managed master data. Pet owners select from predefined species.
        | - Prevents typos/inconsistencies (e.g., "Dog" vs "dog" vs "Dogs").
        | - `status` allows disabling species without deleting (soft-disable).
        */

        Schema::create('species', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            $table->enum('status', [
                'active',
                'inactive'
            ])->default('active');

            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | 6. BREEDS
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "Manage Pet Profiles — species, breed."
        |
        | DESIGN DECISION:
        | - Each breed belongs to one species (e.g., "Labrador" belongs to "Dog").
        | - `unique(['species_id', 'name'])` prevents duplicate breeds within species.
        | - `restrictOnDelete` — can't delete a species that has breeds assigned.
        | - Owner selects breed after selecting species (cascading dropdown).
        */

        Schema::create('breeds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('species_id')
                ->constrained('species')
                ->restrictOnDelete();

            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            $table->enum('status', [
                'active',
                'inactive'
            ])->default('active');

            $table->timestamps();

            $table->unique(['species_id', 'name']);
        });


        /*
        |--------------------------------------------------------------------------
        | 7. VET SPECIALIZATIONS
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "Veterinarian — add specialization, experience."
        |
        | DESIGN DECISION:
        | - Admin-managed master data. Vets select from predefined specializations.
        | - Examples: "Surgery", "Dermatology", "Cardiology", "Dentistry".
        | - Prevents free-text inconsistencies.
        */

        Schema::create('specializations', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();
            $table->text('description')->nullable();

            $table->enum('status', [
                'active',
                'inactive'
            ])->default('active');

            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | 8. VET SPECIALIZATIONS PIVOT
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "One vet can have multiple specializations."
        |
        | DESIGN DECISION:
        | - Many-to-many: one vet → multiple specializations, one specialization → multiple vets.
        | - `vet_id` references `users` (not `vet_profiles`) because the vet IS a user.
        | - `unique(['vet_id', 'specialization_id'])` prevents duplicate assignments.
        */

        Schema::create('vet_specializations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vet_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('specialization_id')
                ->constrained('specializations')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique([
                'vet_id',
                'specialization_id'
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | 9. PETS
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "Manage Pet Profiles — add/edit/view/delete pets: name,
        | species, breed, age, medical history."
        | SRS Reference: "Multiple pets per owner allowed."
        | SRS Reference: "Image galleries per pet."
        |
        | DESIGN DECISION:
        | - `owner_id` → users. One owner has many pets. Cascade delete: if owner
        |   account is deleted, their pets are also removed.
        | - `species_id` required, `breed_id` nullable (some species have no breed
        |   distinction, e.g., "Mixed").
        | - `profile_image` — main photo. Gallery images in separate `pet_images` table.
        | - `date_of_birth` used to calculate age dynamically (not stored as integer)
        |   so age is always accurate without manual updates.
        | - `is_neutered` — health-relevant, commonly tracked by vets.
        | - `microchip_number` — standard pet identification, unique if present.
        | - `adoption_fee` — only used for shelter pets listed for adoption.
        */

        Schema::create('pets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('species_id')
                ->constrained('species')
                ->restrictOnDelete();

            $table->foreignId('breed_id')
                ->nullable()
                ->constrained('breeds')
                ->nullOnDelete();

            $table->string('name');

            $table->enum('gender', [
                'male',
                'female'
            ])->nullable();

            $table->date('date_of_birth')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('color')->nullable();
            $table->text('description')->nullable();

            $table->string('profile_image')->nullable();

            // Health tracking
            $table->boolean('is_neutered')->default(false);
            $table->string('microchip_number')->nullable();

            // Only relevant for shelter pets listed for adoption
            $table->decimal('adoption_fee', 8, 2)->nullable();

            $table->timestamps();

            $table->index('owner_id');
            $table->index('species_id');
            $table->index('breed_id');
        });


        /*
        |--------------------------------------------------------------------------
        | 10. PET IMAGES
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "Image galleries per pet; tabbed UI for multiple pets."
        |
        | DESIGN DECISION:
        | - Polymorphic would be overkill here — pets are the only entity with
        |   a gallery. Dedicated table is simpler and faster.
        | - `caption` optional — owner can label images ("At the park", "After surgery").
        | - Cascade delete: removing a pet removes all its images.
        */

        Schema::create('pet_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pet_id')
                ->constrained('pets')
                ->cascadeOnDelete();

            $table->string('image_path');
            $table->string('caption')->nullable();

            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | 11. HEALTH RECORDS
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "Track Health Records — create/update/view vaccination dates,
        | allergies, illnesses, treatment history."
        | SRS Reference: "Visual timeline of vaccinations/treatments/milestones."
        | SRS Reference: "Upload/store vet certificates, X-rays, lab reports."
        |
        | DESIGN DECISION:
        | - `record_date` used for timeline sorting (chronological view).
        | - `record_type` — free-form string (e.g., "checkup", "surgery", "lab_result").
        |   Keeping it flexible rather than enum allows future expansion.
        | - `vet_id` nullable — owner can add personal records (home observations)
        |   without a vet involvement.
        | - Composite index on [pet_id, record_date] for fast timeline queries.
        */

        Schema::create('health_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pet_id')
                ->constrained('pets')
                ->cascadeOnDelete();

            $table->foreignId('vet_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('record_date');

            $table->string('record_type')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index([
                'pet_id',
                'record_date'
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | 12. VACCINATIONS
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "Track Health Records — vaccination dates."
        | SRS Reference: "Visual timeline of vaccinations/treatments/milestones."
        |
        | DESIGN DECISION:
        | - Separate from health_records because vaccinations have unique fields
        |   (vaccine_name, batch_number, next_due_date) that don't apply to
        |   general health records.
        | - `next_due_date` enables reminder notifications ("Your pet's vaccination
        |   is due in 3 days").
        | - `batch_number` — traceability for vaccine batches (required by some
        |   veterinary regulations).
        */

        Schema::create('vaccinations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pet_id')
                ->constrained('pets')
                ->cascadeOnDelete();

            $table->string('vaccine_name');

            $table->date('vaccination_date')->nullable();
            $table->date('next_due_date')->nullable();

            $table->string('batch_number')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('pet_id');
        });


        /*
        |--------------------------------------------------------------------------
        | 13. MEDICAL DOCUMENTS
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "Upload/store vet certificates, X-rays, lab reports."
        |
        | DESIGN DECISION:
        | - Stores file references (path, name) not actual file content.
        | - `document_type` — categorizes files (e.g., "xray", "lab_report",
        |   "certificate", "prescription_image").
        | - `health_record_id` nullable — document may exist without being linked
        |   to a specific health record (e.g., standalone certificate upload).
        | - `pet_id` required — every document belongs to a pet.
        */

        Schema::create('medical_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pet_id')
                ->constrained('pets')
                ->cascadeOnDelete();

            $table->foreignId('health_record_id')
                ->nullable()
                ->constrained('health_records')
                ->nullOnDelete();

            $table->string('document_type')->nullable();

            $table->string('file_name');
            $table->string('file_path');

            $table->text('description')->nullable();

            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | 14. INSURANCE POLICIES
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "Upload/store/view insurance policy details & claims —
        | view-only scope, no claims processing."
        |
        | DESIGN DECISION:
        | - View-only per SRS. No payment/claims processing logic.
        | - `policy_document` and `claim_document` store file paths for uploaded
        |   PDFs/images of insurance paperwork.
        | - `pet_id` — each policy is tied to a specific pet.
        */

        Schema::create('insurance_policies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pet_id')
                ->constrained('pets')
                ->cascadeOnDelete();

            $table->string('provider_name')->nullable();
            $table->string('policy_number')->nullable();

            $table->date('start_date')->nullable();
            $table->date('expiry_date')->nullable();

            $table->string('policy_document')->nullable();
            $table->string('claim_document')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | 15. VET AVAILABILITIES
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "Veterinarian — available time slots."
        | SRS Reference: "Appointment Booking — request/book appointments with
        | listed vets."
        |
        | DESIGN DECISION:
        | - One row per day-of-week per vet. Vet sets their weekly schedule.
        | - `is_available` — vet can disable a specific day without deleting the row.
        | - `start_time`/`end_time` — the window when the vet accepts appointments.
        |   Individual appointment slots are derived from this (e.g., 30-min increments).
        | - Composite index on [vet_id, day_of_week] for fast availability lookups
        |   during appointment booking.
        */

        Schema::create('vet_availabilities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vet_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('day_of_week', [
                'monday',
                'tuesday',
                'wednesday',
                'thursday',
                'friday',
                'saturday',
                'sunday'
            ]);

            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            $table->boolean('is_available')->default(true);

            $table->timestamps();

            $table->index([
                'vet_id',
                'day_of_week'
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | 16. APPOINTMENTS
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "Appointment Booking — request/book appointments with
        | listed vets."
        | SRS Reference: "Auto-suggest vets based on pet condition or location."
        | SRS Reference: "Veterinarian — manage appointments: view upcoming,
        | approve/reschedule, update availability."
        |
        | DESIGN DECISION:
        | - Links three parties: pet, owner, vet. All three IDs stored for fast
        |   queries without joins (e.g., "show me all appointments for this vet
        |   on this date").
        | - `status` enum covers full lifecycle: pending → approved → completed,
        |   or pending → rejected, or approved → rescheduled, or any → cancelled.
        | - `reason` — owner's description of why they're booking.
        | - `notes` — vet's private notes (not visible to owner).
        | - Composite indexes on [vet_id, date] and [owner_id, date] for
        |   calendar views.
        */

        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pet_id')
                ->constrained('pets')
                ->cascadeOnDelete();

            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('vet_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('appointment_date');
            $table->time('appointment_time');

            $table->text('reason')->nullable();

            $table->enum('status', [
                'pending',
                'approved',
                'rescheduled',
                'completed',
                'cancelled',
                'rejected'
            ])->default('pending');

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index([
                'vet_id',
                'appointment_date'
            ]);

            $table->index([
                'owner_id',
                'appointment_date'
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | 17. TREATMENTS
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "Veterinarian — log treatments/observations: diagnosis,
        | prescribed medication, follow-up actions."
        | SRS Reference: "Structured view (symptoms, past treatments, lab results,
        | prescriptions)."
        |
        | DESIGN DECISION:
        | - Linked to appointment (one appointment → one treatment record).
        | - `symptoms`, `diagnosis`, `treatment` are separate text fields for
        |   structured display in the vet's treatment view.
        | - `follow_up_date` — enables reminder notifications for follow-up visits.
        | - `pet_id` and `vet_id` denormalized from appointment for fast queries
        |   (e.g., "show all treatments for this pet" without joining appointments).
        */

        Schema::create('treatments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('appointment_id')
                ->constrained('appointments')
                ->cascadeOnDelete();

            $table->foreignId('pet_id')
                ->constrained('pets')
                ->cascadeOnDelete();

            $table->foreignId('vet_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('symptoms')->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('treatment')->nullable();

            $table->date('follow_up_date')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | 18. PRESCRIPTIONS
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "Veterinarian — prescribed medication."
        | SRS Reference: "Structured view — prescriptions."
        |
        | DESIGN DECISION:
        | - One treatment can have multiple prescriptions (multiple medicines).
        | - `dosage`, `frequency`, `duration` — structured fields for clear
        |   medication instructions (not free-text).
        | - `instructions` — additional notes ("Take with food", "Avoid sunlight").
        */

        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('treatment_id')
                ->constrained('treatments')
                ->cascadeOnDelete();

            $table->string('medicine_name');
            $table->string('dosage')->nullable();
            $table->string('frequency')->nullable();
            $table->string('duration')->nullable();

            $table->text('instructions')->nullable();

            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | 19. PRODUCT CATEGORIES
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "View/Purchase Products — browse categories with filters."
        | SRS Reference: "Admin — manage Products & Categories (CRUD)."
        |
        | DESIGN DECISION:
        | - Extends the starter kit's existing categories pattern (slug, soft deletes,
        |   audit columns) but adds `parent_id` for nested categories.
        | - `parent_id` — allows hierarchical categories (e.g., "Dog Food" under
        |   "Food", "Leashes" under "Accessories"). Self-referencing FK, nullable
        |   for top-level categories.
        | - `slug` — SEO-friendly URLs (/categories/dog-food instead of /categories/3).
        | - `sort_order` — admin controls display order.
        | - `is_active` — disable category without deleting (preserves product links).
        | - `created_by`/`updated_by` — audit trail (who created/modified this category).
        | - `softDeletes` — prevent accidental data loss. Starter kit pattern.
        */

        Schema::create('categories', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            // Nested categories: nullable FK to self
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();

            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Audit columns (starter kit pattern)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });


        /*
        |--------------------------------------------------------------------------
        | 20. PRODUCTS
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "View/Purchase Products — view product details, add/remove/
        | modify cart items."
        | SRS Reference: "Admin — manage Products (CRUD, stock levels, pricing)."
        |
        | DESIGN DECISION:
        | - `slug` — SEO-friendly URLs (/products/premium-dog-food).
        | - `sku` — stock-keeping unit for inventory tracking (admin reference).
        | - `price` — displayed to owners, stored in cart_items/order_items as snapshot.
        | - `stock_quantity` — admin manages inventory. Product shows "out of stock"
        |   when quantity = 0.
        | - `is_featured` — for homepage featured products section.
        | - `meta_title`/`meta_description` — SEO fields (starter kit already has
        |   site-level SEO settings; product-level SEO is more granular).
        | - `created_by`/`updated_by` — audit trail.
        */

        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->string('sku')->unique()->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->decimal('weight', 8, 2)->nullable();

            $table->boolean('is_featured')->default(false);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();

            $table->enum('status', [
                'active',
                'inactive',
                'out_of_stock'
            ])->default('active');

            $table->timestamps();

            // Audit columns
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->index('category_id');
            $table->index('is_featured');
        });


        /*
        |--------------------------------------------------------------------------
        | 21. PRODUCT IMAGES
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "View/Purchase Products — view product details."
        |
        | DESIGN DECISION:
        | - Multiple images per product (gallery view on product detail page).
        | - `is_primary` — one image is the main/cover image for listings.
        | - `sort_order` — admin controls display order.
        | - Separate from products table because one product → many images,
        |   and a single VARCHAR can't hold multiple file paths.
        */

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->string('image_path');
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | 22. CARTS
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "View/Purchase Products — add/remove/modify cart items."
        | SRS Constraint: "No payment gateway — cart/browse only, no checkout."
        |
        | DESIGN DECISION:
        | - One cart per owner (user_id is unique). Cart is ephemeral — exists
        |   only while the owner is actively browsing.
        | - `carts` table is minimal because cart items hold the real data.
        | - Cascade delete: if owner account is deleted, their cart is removed.
        */

        Schema::create('carts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('owner_id')
                ->unique()
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | 23. CART ITEMS
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "add/remove/modify cart items."
        |
        | DESIGN DECISION:
        | - `price` stored here (snapshot) so cart total remains stable even if
        |   product price changes after item was added.
        | - `unique(['cart_id', 'product_id'])` — can't add same product twice;
        |   instead, modify quantity.
        | - `quantity` — owner can increase/decrease.
        */

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cart_id')
                ->constrained('carts')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            $table->unsignedInteger('quantity')->default(1);

            // Price snapshot at time of adding to cart
            $table->decimal('price', 10, 2);

            $table->timestamps();

            $table->unique([
                'cart_id',
                'product_id'
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | 24. ORDERS
        |--------------------------------------------------------------------------
        |
        | SRS Constraint: "No payment gateway — cart/browse only, no checkout,
        | no delivery tracking."
        |
        | DESIGN DECISION:
        | - Orders exist as a record of intent, not financial transactions.
        |   SRS says no payment — so orders are informational only.
        | - `order_number` — human-readable ID (e.g., "ORD-2026-0001") for
        |   easy reference in notifications and admin panel.
        | - `shipping_address` — placeholder for future extensibility (SRS says
        |   no delivery, but the field costs nothing and makes the schema ready
        |   if requirements change).
        | - `status_history` JSON — tracks status transitions with timestamps
        |   (e.g., [{status: "placed", at: "..."}, {status: "processing", at: "..."}]).
        */

        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('order_number')->unique();
            $table->decimal('total_amount', 10, 2);

            $table->enum('status', [
                'placed',
                'processing',
                'completed',
                'cancelled'
            ])->default('placed');

            $table->text('shipping_address')->nullable();
            $table->text('notes')->nullable();

            // Status transition log: [{status, at, by}]
            $table->json('status_history')->nullable();

            $table->dateTime('order_date');

            $table->timestamps();

            $table->index([
                'owner_id',
                'status'
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | 25. ORDER ITEMS
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "View/Purchase Products — cart/browse only."
        |
        | DESIGN DECISION:
        | - `price_each` — snapshot at time of order (same logic as cart_items).
        |   Product price may change after order is placed; order record must
        |   reflect the price the owner actually saw.
        | - `quantity` — how many of this product were ordered.
        */

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            $table->unsignedInteger('quantity');

            // Price snapshot at time of order
            $table->decimal('price_each', 10, 2);

            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | 26. ADOPTION LISTINGS
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "Animal Shelter — list adoptable pets: images, age, breed,
        | health status, details."
        | SRS Reference: "Coordinate with adopters: view interest forms, respond,
        | finalize adoption."
        |
        | DESIGN DECISION:
        | - `shelter_id` → users (the shelter user who created this listing).
        | - `species_id`/`breed_id` — for filtering (owner browses by species/breed).
        | - `pet_name` — name of the animal (not linked to `pets` table because
        |   shelter animals may not have owner-created pet profiles).
        | - `age` stored as string (e.g., "2 years", "3 months") because exact
        |   DOB may be unknown for shelter animals.
        | - `status` — full lifecycle: available → pending (application received) →
        |   adopted (application completed) / inactive (shelter removed listing).
        | - Composite indexes for filtering: [species_id, breed_id] and
        |   [shelter_id, status].
        */

        Schema::create('adoption_listings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('shelter_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('species_id')
                ->constrained('species')
                ->restrictOnDelete();

            $table->foreignId('breed_id')
                ->nullable()
                ->constrained('breeds')
                ->nullOnDelete();

            $table->string('pet_name');
            $table->string('age')->nullable();

            $table->enum('gender', [
                'male',
                'female'
            ])->nullable();

            $table->string('health_status')->nullable();

            $table->text('description')->nullable();

            $table->enum('status', [
                'available',
                'pending',
                'adopted',
                'inactive'
            ])->default('available');

            $table->timestamps();

            $table->index([
                'species_id',
                'breed_id'
            ]);

            $table->index([
                'shelter_id',
                'status'
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | 27. ADOPTION IMAGES
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "list adoptable pets: images."
        |
        | DESIGN DECISION:
        | - Multiple images per listing (gallery view).
        | - Same pattern as pet_images and product_images.
        */

        Schema::create('adoption_images', function (Blueprint $table) {
            $table->id();

            $table->foreignId('listing_id')
                ->constrained('adoption_listings')
                ->cascadeOnDelete();

            $table->string('image_path');
            $table->string('caption')->nullable();

            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | 28. ADOPTION APPLICATIONS
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "Coordinate with adopters: view interest forms, respond,
        | finalize adoption via email/push notification."
        |
        | DESIGN DECISION:
        | - Links an applicant (user) to a listing. One user can apply to multiple
        |   listings; one listing can receive multiple applications.
        | - `message` — applicant's message to the shelter ("I have a big yard...").
        | - `shelter_response` — shelter's reply before approving/rejecting.
        | - `status` — pending → approved/rejected → completed (adoption finalized).
        | - Composite indexes for fast queries: [listing_id, status] for shelter's
        |   inbox, [applicant_id, status] for owner's "my applications" view.
        */

        Schema::create('adoption_applications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('listing_id')
                ->constrained('adoption_listings')
                ->cascadeOnDelete();

            $table->foreignId('applicant_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->text('message')->nullable();

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'completed'
            ])->default('pending');

            $table->text('shelter_response')->nullable();

            $table->timestamps();

            $table->index([
                'listing_id',
                'status'
            ]);

            $table->index([
                'applicant_id',
                'status'
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | 29. CARE CONTENTS
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "Access Care Options — categorized care content (feeding,
        | hygiene, exercise) via articles/videos/FAQs."
        | SRS Reference: "Optional AI chatbot for care queries."
        |
        | DESIGN DECISION:
        | - `category` enum — predefined care topics (feeding, hygiene, exercise,
        |   health, training). Keeps content organized.
        | - `content_type` enum — article (text), video (embedded), faq (Q&A format).
        | - `content` longText — HTML content for articles, embed code for videos,
        |   JSON for FAQ structure.
        | - `media_url` — video URL or external link.
        | - `thumbnail` — preview image for listing pages.
        | - `status` — admin can publish/unpublish without deleting.
        */

        Schema::create('care_contents', function (Blueprint $table) {
            $table->id();

            $table->string('title');

            $table->enum('category', [
                'feeding',
                'hygiene',
                'exercise',
                'health',
                'training'
            ]);

            $table->enum('content_type', [
                'article',
                'video',
                'faq'
            ]);

            $table->longText('content')->nullable();

            $table->string('media_url')->nullable();
            $table->string('thumbnail')->nullable();

            $table->enum('status', [
                'active',
                'inactive'
            ])->default('active');

            $table->timestamps();
        });


        /*
        |--------------------------------------------------------------------------
        | 30. NOTIFICATIONS
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "Notifications — shared table, applies to all 4 roles."
        | - Owners: vaccination reminders, appointment updates, product alerts.
        | - Vets: new/changed appointment requests.
        | - Shelters: adopter inquiries.
        | - Admin: new pending verifications.
        |
        | DESIGN DECISION:
        | - Simple in-app notification table (not Laravel's built-in notifications
        |   table which uses UUIDs and different structure).
        | - `type` — categorizes notifications (e.g., "appointment", "vaccination",
        |   "adoption", "verification", "order").
        | - `link` — clickable URL to navigate to the relevant page.
        | - `is_read` — boolean for unread badge count in topbar.
        | - Composite index on [user_id, is_read] for fast "unread count" queries.
        */

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title');
            $table->text('message');

            $table->string('type')->nullable();
            $table->string('link')->nullable();

            $table->boolean('is_read')->default(false);

            $table->timestamps();

            $table->index([
                'user_id',
                'is_read'
            ]);
        });


        /*
        |--------------------------------------------------------------------------
        | 31. REVIEWS
        |--------------------------------------------------------------------------
        |
        | SRS Reference: "Ratings & Reviews — owners rate vets/shelters/products;
        | Admin can moderate."
        |
        | DESIGN DECISION:
        | - Polymorphic: one review table serves vets, adoption listings (shelters),
        |   and products. `reviewable_type` + `reviewable_id` point to the target.
        | - `rating` — 1-5 scale (tiny integer, max value 5).
        | - `comment` — optional text review.
        | - `user_id` — the reviewer. One user can leave multiple reviews but only
        |   one per target (enforced at application level, not DB unique constraint
        |   because polymorphic unique constraints are complex in PostgreSQL).
        | - Admin moderation handled at application level (admin can hide/delete
        |   reviews from the admin panel). No `is_flagged` column — moderation
        |   is done through the admin review management page, not a flagging system.
        |   This keeps the schema simple for a competition build.
        */

        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Polymorphic: reviewable can be User (vet), AdoptionListing, or Product
            $table->string('reviewable_type');
            $table->unsignedBigInteger('reviewable_id');

            $table->unsignedTinyInteger('rating');  // 1-5

            $table->text('comment')->nullable();

            $table->timestamps();

            $table->index([
                'reviewable_type',
                'reviewable_id'
            ]);
        });
    }


    /*
    |--------------------------------------------------------------------------
    | REVERSE ORDER
    |--------------------------------------------------------------------------
    | Drop tables in reverse order of creation to respect foreign key constraints.
    */

    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('care_contents');

        Schema::dropIfExists('adoption_applications');
        Schema::dropIfExists('adoption_images');
        Schema::dropIfExists('adoption_listings');

        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');

        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');

        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');

        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('treatments');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('vet_availabilities');

        Schema::dropIfExists('insurance_policies');
        Schema::dropIfExists('medical_documents');
        Schema::dropIfExists('vaccinations');
        Schema::dropIfExists('health_records');

        Schema::dropIfExists('pet_images');
        Schema::dropIfExists('pets');

        Schema::dropIfExists('vet_specializations');
        Schema::dropIfExists('specializations');

        Schema::dropIfExists('breeds');
        Schema::dropIfExists('species');

        Schema::dropIfExists('shelter_profiles');
        Schema::dropIfExists('vet_profiles');

        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('users');
    }
};
```
