# FurShield — Architecture & Database Decisions

> This document records every architectural decision made during the FurShield build.
> Each decision includes: **what was decided**, **why**, and **SRS reference**.

---

## 1. Single `users` Table for All Roles

**Decision:** One `users` table holds admin, pet owner, veterinarian, and animal shelter accounts.

**Why:**
- Laravel Breeze auth scaffolding (login, register, password reset, email verification, sessions) is built around a single `users` table.
- Reusing it avoids duplicating login/password-reset/session logic 4 times.
- One login form, one auth guard, one password reset flow for all roles.

**SRS Reference:** "Pet Owner Registration & Login", "Veterinarian Registration", "Animal Shelter Registration" — all require name, contact, email, address.

---

## 2. Role Column + `role_user` Pivot (Both)

**Decision:** Users table has a `role` enum for quick checks AND a `role_user` pivot for full RBAC.

**Why:**
- `role` enum: Post-login redirect (`admin → /admin/dashboard`, `owner → /owner/dashboard`, etc.) needs a fast check without a pivot query.
- `role_user` pivot: Allows the existing `HasRoles` trait, `RoleMiddleware`, and `role:admin` middleware to work without modification. Supports multi-role if needed.
- The starter kit already uses this pattern — we kept it.

**SRS Reference:** "Role-Based Access Control (RBAC)" — each role only sees its own features.

---

## 3. Role-Specific Extension Tables (1:1)

**Decision:** `vet_profiles` and `shelter_profiles` are 1:1 with `users` via `user_id` FK (unique).

**Why:**
- Keeps `users` table lean — no 30+ columns on one table.
- Only populated for the matching role (vet/shelter). Empty for other roles.
- Profile data is role-specific and shouldn't clutter the shared users table.

**SRS Reference:** "Veterinarian — post-login: add specialization, experience, available time slots." "Animal Shelter — register with shelter name, contact person, email."

---

## 4. Verification Gating (Admin Approval)

**Decision:** Vets and shelter accounts start as `pending_verification`. Admin must approve before they appear in public listings.

**Why:**
- SRS explicitly states shelters need "a verified account" and vets should be "listed."
- Someone must flip the verification flag — Admin performs this.
- Until verified: can log in and complete profile, but cannot appear in public vet/shelter listings or accept bookings/listings.

**Fields:**
- `users.status` — `pending_verification` → `active` (after admin approval)
- `vet_profiles.is_verified` + `verified_at` + `verified_by`
- `shelter_profiles.is_verified` + `verified_at` + `verified_by`

**SRS Reference:** "Verified login — SRS explicitly requires a verified account; Admin performs verification."

---

## 5. Pet Owner Has No Extension Table

**Decision:** Pet owner data lives entirely on `users` table. No `owner_profiles` table.

**Why:**
- `users` fields (name, email, phone, address) cover everything a pet owner needs.
- Pets link to `users.id` as `owner_id`.
- Adding an empty extension table for owners adds complexity with zero benefit.

**SRS Reference:** "Pet Owner — name, contact number, email, address. Multiple pets per owner."

---

## 6. Profile Photo on `users` Table (Shared)

**Decision:** `profile_image` is on the `users` table, NOT on `vet_profiles` or `shelter_profiles`.

**Why:**
- All 4 roles need a profile photo — it's shared functionality.
- Storing it once on `users` avoids duplication and simplifies profile editing.
- One upload endpoint, one storage path, one relationship.

---

## 7. Species & Breeds as Admin-Managed Master Data

**Decision:** Species and breeds are separate tables managed by Admin, not free-text fields on pets.

**Why:**
- Prevents typos/inconsistencies ("Dog" vs "dog" vs "Dogs").
- Enables filtering by species/breed across the platform.
- Admin controls which species/breeds are available.
- Breeds belong to a species (e.g., "Labrador" belongs to "Dog").

**SRS Reference:** "Manage Pet Profiles — name, species, breed, age."

---

## 8. Nested Product Categories

**Decision:** `categories` table has `parent_id` (self-referencing FK) for hierarchical categories.

**Why:**
- Allows nested categories: "Dog Food" under "Food", "Leashes" under "Accessories".
- `parent_id` nullable — top-level categories have no parent.
- Same pattern as WordPress/WooCommerce categories.

**SRS Reference:** "View/Purchase Products — browse categories with filters."

---

## 9. Categories Table Replaced (Not Altered)

**Decision:** Old starter-kit `categories` table is dropped and recreated with FurShield schema.

**Why:**
- Old table had: `name`, `slug`, `description`, `is_active`, `sort_order`, `created_by`, `updated_by`.
- New table adds: `image`, `parent_id`, `softDeletes`.
- The new migration (`2026_09_12_000018`) drops and recreates the table cleanly.

---

## 10. Polymorphic Reviews

**Decision:** One `reviews` table serves vets, adoption listings, and products via polymorphic `reviewable_type` + `reviewable_id`.

**Why:**
- Owners rate vets, adoption listings (shelters), and products — three different target types.
- Polymorphic avoids creating `vet_reviews`, `product_reviews`, `listing_reviews` tables.
- One controller, one model, one set of views for all review types.
- Admin moderation handled at application level (admin panel), not via `is_flagged` columns.

**SRS Reference:** "Ratings & Reviews — owners rate vets/shelters/products; Admin can moderate."

---

## 11. Notifications Table (Custom, Not Laravel Built-in)

**Decision:** Custom `furshield_notifications` table instead of Laravel's built-in notifications table.

**Why:**
- Laravel's built-in table uses UUIDs and a different structure.
- Our table is simpler: `user_id`, `title`, `message`, `type`, `link`, `is_read`.
- `link` field enables clickable notifications that navigate to relevant pages.
- `type` categorizes notifications (appointment, vaccination, adoption, verification, order).

**SRS Reference:** "Notifications — shared table, applies to all 4 roles."

---

## 12. Appointments Link Three Parties

**Decision:** `appointments` stores `pet_id`, `owner_id`, and `vet_id` (all denormalized).

**Why:**
- Fast queries without joins: "show me all appointments for this vet on this date."
- All three IDs needed for authorization checks and display.
- `status` enum covers full lifecycle: pending → approved → completed, or any → cancelled.

**SRS Reference:** "Appointment Booking — request/book appointments with listed vets."

---

## 13. Treatments Linked to Appointments

**Decision:** One appointment → one treatment record. `pet_id` and `vet_id` denormalized.

**Why:**
- Denormalization avoids joins for common queries ("show all treatments for this pet").
- `symptoms`, `diagnosis`, `treatment` are separate fields for structured display.
- `follow_up_date` enables reminder notifications.

**SRS Reference:** "Veterinarian — log treatments/observations: diagnosis, prescribed medication, follow-up actions."

---

## 14. Price Snapshots in Cart & Orders

**Decision:** `cart_items.price` and `order_items.price_each` store the price at time of adding/ordering.

**Why:**
- Product price may change after an item is added to cart or ordered.
- Cart total and order total must reflect the price the owner actually saw.
- Historical accuracy for order records.

**SRS Constraint:** "No payment gateway — cart/browse only, no checkout."

---

## 15. Orders Exist Despite No Payment

**Decision:** `orders` table exists even though SRS says "no payment gateway."

**Why:**
- Orders record intent, not financial transactions.
- `order_number` provides human-readable reference.
- `status_history` JSON tracks status transitions.
- `shipping_address` placeholder for future extensibility.
- Admin can still see order activity in dashboard stats.

**SRS Constraint:** "No payment, no delivery — cart/browse only."

---

## 16. Soft Deletes Only Where Critical

**Decision:** Only `categories` uses soft deletes. All other tables use hard deletes.

**Why:**
- Categories are referenced by products — accidental deletion would orphan products.
- All other entities (pets, appointments, orders) are user-owned and safe to hard-delete.
- Keeps the schema clean for a competition build.

---

## 17. Starter Kit Tables Kept

**Decision:** Old starter-kit tables (media, settings, tags, activity_logs, contacts, subscribers) are kept alongside new FurShield tables.

**Why:**
- These tables are deeply integrated: models, controllers, views, routes, seeders, dashboard stats all reference them.
- Removing them would break existing functionality without immediate benefit.
- They don't conflict with FurShield tables.
- Can be cleaned up after the competition if needed.

---

## 18. Seed Data Strategy

**Decision:** Comprehensive seeders with realistic data across all roles.

**Why:**
- Competition demo requires real-looking data, not empty tables.
- Seeds demonstrate all features: multiple roles, pets, appointments, products, adoption listings.
- One password (`password`) for all seeded users for easy demo login.

**Seeded Data:**
- 1 admin, 3 pet owners, 3 vets (2 verified, 1 pending), 2 shelters (1 verified, 1 pending)
- 5 species with 4-7 breeds each
- 8 veterinary specializations
- 6 product categories, 2+ products
- 4 pets with health records and vaccinations
- 1 appointment with treatment and prescription
- 2 adoption listings
- 2 notifications, 1 review

---

## 19. Profile Management Architecture

**Decision:** Single `ProfileController` with two tabs: "Personal Info" (shared) and "Role Details" (conditional).

**Why:**
- One controller, one view, one form for all roles.
- Tab 1: Shared user fields (name, email, phone, address, photo) — same for all.
- Tab 2: Role-specific fields (vet: specializations, qualifications, clinic; shelter: shelter name, description, city; owner: pet list).
- Role detected from `auth()->user()->role` to conditionally show Tab 2 content.

---

## 20. File Naming Convention for Migrations

**Decision:** Sequential numbering with descriptive names.

**Format:** `YYYY_MM_DD_NNNNN_description.php`

**Order:**
1. `000001` — Extend users table
2. `000002` — vet_profiles
3. `000003` — shelter_profiles
4. `000004` — species
5. `000005` — breeds
6. `000006` — specializations
7. `000007` — vet_specializations pivot
8. `000008` — pets
9. `000009` — pet_images
10. `000010` — health_records
11. `000011` — vaccinations
12. `000012` — medical_documents
13. `000013` — insurance_policies
14. `000014` — vet_availabilities
15. `000015` — appointments
16. `000016` — treatments
17. `000017` — prescriptions
18. `000018` — replace categories
19. `000019` — products
20. `000020` — product_images
21. `000021` — carts + cart_items
22. `000022` — orders + order_items
23. `000023` — adoption_listings + adoption_images + adoption_applications
24. `000024` — care_contents
25. `000025` — furshield_notifications
26. `000026` — reviews

---

*Last updated: September 12, 2026*
