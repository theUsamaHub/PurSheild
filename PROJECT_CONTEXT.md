# FurShield — Project Context
**TechWiz 6 — Global AI-Based Tech Competition | Aptech Limited**
**Stack:** Laravel (starter kit with auth scaffolding already in place) + MySQL/PostgreSQL
**Build window:** 6 days

> This file is the single reference for scope, roles, data model direction, and security rules for the FurShield build. The full physical schema lives in **`databaseschema.md`** (companion file, kept in sync with this one).

---

## 1. Roles (4 total — SRS gives 3, we added Admin)

| # | Role | Source |
|---|---|---|
| 1 | **Admin** | Added by team — not in SRS, see justification below |
| 2 | Pet Owner | SRS |
| 3 | Veterinarian | SRS |
| 4 | Animal Shelter | SRS |

### Why we added Admin (critical thinking on the SRS gap)

The SRS describes Pet Owners *browsing/purchasing* products, but it never says **who creates, edits, prices, or removes products, categories, or stock**. Without an Admin:

- No one owns the **Products/Categories** catalog (add/edit/delete, stock levels, pricing).
- No one can **moderate** shelter listings, vet registrations, or reported reviews.
- No one can **verify/approve** vet or shelter accounts (SRS says shelters need "a verified account" and vets should be listed — someone has to flip that verification flag).
- No one has a system-wide view for **user management** (disable abusive accounts, reset access) or **content management** (Care Articles/FAQs, About Us, Contact Us static content).
- Non-functional requirements like "only registered users access certain features" and "the app should be reliable" imply someone administers that — an Admin role is the standard way full-stack apps satisfy this without hardcoding.

**Admin capabilities (our addition, layered on top of SRS scope):**
- Manage Products & Categories (create/edit/delete, stock, pricing) — this is the missing piece that makes the "View/Purchase Products" feature actually work.
- Approve/verify Veterinarian and Animal Shelter registrations.
- Manage/moderate Ratings & Reviews (remove abusive content).
- Manage Care Articles/FAQs content.
- View/manage all Users (disable/enable accounts).
- Manage static content: About Us, Contact Us.
- System-wide dashboard (counts of owners, pets, vets, shelters, appointments, orders/cart activity).

This does **not** violate the SRS constraint of "no payment gateway" — Admin manages the catalog, not transactions (there are none to manage; cart/browse only, as specified).

---

## 2. Single Source of Truth for Login: one `users` table

Instead of separate `owners`, `vets`, `shelters` tables each with their own auth, we use **one `users` table** for all 4 roles, with a `role_id` foreign key into a `roles` lookup table.

**Why:**
- Laravel's starter kit auth scaffolding (Breeze/Jetstream-style) is already built around a single `users` table — reusing it means we don't fight the framework or duplicate login/password-reset/session logic four times.
- One login form, one auth guard, one password reset flow, one session mechanism for all roles.
- Role-specific fields (specialization for vets, shelter name for shelters, address for owners) live in **role-detail tables** that reference `users.id` — keeps `users` lean and generic.

```
users
├── id (PK)
├── name
├── email (unique)
├── password (hashed)
├── role_id (FK → roles.id)
├── phone
├── address
├── status (active/disabled/pending-verification)
├── email_verified_at
├── created_at / updated_at

roles
├── id (PK)
├── name   -- 'admin' | 'pet_owner' | 'veterinarian' | 'animal_shelter'
```

Role-specific extension tables (1:1 with `users`, only populated for the matching role):
- `vet_profiles` (user_id FK, specialization, experience_years, is_verified, ...)
- `shelter_profiles` (user_id FK, shelter_name, contact_person, is_verified, ...)
- Pet Owner needs no extension table — `users` fields cover it; `pets` links to `users.id` as owner.

Full column-level detail is in **`databaseschema.md`**.

---

## 3. Functional Requirements by Role

### 3.1 Admin (our addition)
- Login (via shared `users` table, `role = admin`).
- Dashboard: system-wide stats.
- Manage Products & Categories (CRUD).
- Approve/reject/verify Vet and Shelter registrations.
- Manage all Users (view, disable/enable, reset).
- Moderate Ratings & Reviews.
- Manage Care Articles/FAQs.
- Manage static About Us / Contact Us content.

### 3.2 Pet Owner (SRS)
**Registration & Login**
- Register/login; fields: name, contact number, email, address.
- Multiple pets per owner allowed.
- Optional family account sharing.

**Manage Pet Profiles**
- Add/edit/view/delete pets: name, species, breed, age, medical history.
- Image galleries per pet; tabbed UI for multiple pets.

**Track Health Records**
- Create/update/view vaccination dates, allergies, illnesses, treatment history.
- Visual timeline of vaccinations/treatments/milestones.
- Upload/store vet certificates, X-rays, lab reports.
- Upload/store/view insurance policy details & claims — **view-only scope, no claims processing.**

**View/Purchase Products**
- Browse categories with filters, view product details, add/remove/modify cart items.
- **No payment, no delivery** — cart/browse only (per SRS constraint).

**Access Care Options**
- Categorized care content (feeding, hygiene, exercise) via articles/videos/FAQs.
- Optional AI chatbot for care queries.

**Appointment Booking**
- Request/book appointments with listed vets.
- Auto-suggest vets based on pet condition or location.

### 3.3 Veterinarian (SRS)
- Register/login; fields: name, contact number, email, address.
- Post-login: add specialization, experience, available time slots (extension profile).
- **Access gated:** can only view medical history of pets whose owners booked an appointment with them.
- Log treatments/observations: diagnosis, prescribed medication, follow-up actions; structured view (symptoms, past treatments, lab results, prescriptions).
- Manage appointments: view upcoming, approve/reschedule, update availability.
- **Admin-verified before going live** (our addition, closes SRS gap on "listed veterinarians").

### 3.4 Animal Shelter (SRS)
- Register with shelter name, contact person, email, contact number, address.
- **Verified login** — SRS explicitly requires a "verified account"; **Admin performs verification** (closes SRS gap).
- List adoptable pets: images, age, breed, health status, details.
- Update pet care status: feeding/grooming/medical logs per animal.
- Coordinate with adopters: view interest forms, respond, finalize adoption via email/push notification.

### 3.5 Common Features (All Roles)
- **Role-Based Access Control (RBAC)** — each role only sees/accesses its own features. See Section 5.
- Search/Sort/Filter — pets, products, care articles, vets (location, breed, type, category).
- Responsive design — mobile-friendly.
- Notifications — shared table, applies to all 4 roles (owners: vaccination/appointment/product alerts; vets: new/changed appointment requests; shelters: adopter inquiries; admin: new pending verifications).
- Ratings & Reviews — owners rate vets/shelters/products; Admin can moderate.
- About Us / Contact Us — static content, editable by Admin, with Google Maps embed.

---

## 4. Constraints (from SRS — unchanged, still apply)

- Must run on major browsers, responsive across devices.
- **No payment gateway** — cart/browse only, no checkout, no delivery tracking.
- No authentication/credential-verification of vets was originally required by SRS — **we're layering Admin verification on top as a deliberate enhancement**, not a contradiction (SRS explicitly allows adding creativity/features beyond the minimum).
- Image/video usage must respect licensing/copyright.
- Boilerplate/HTML templates: **design only**, not functionality.
- No copying content/code from AI tools (ChatGPT etc.); AI-generated *images* are allowed but must be disclosed/credited if used.
- Documentation must contain **no source code**.
- Deliverables: zipped project + ReadMe.doc (assumptions) + `.sql` schema files + mandatory demo video (.mp4) + (preferably) a hosted URL.

---

## 5. Security / Access Control Rules

1. **Single login, role-based redirect.** After login, redirect by `role_id`:
   - `admin` → `/admin/dashboard`
   - `pet_owner` → `/owner/dashboard`
   - `veterinarian` → `/vet/dashboard`
   - `animal_shelter` → `/shelter/dashboard`

2. **Route protection (Laravel middleware / Gates & Policies).**
   - Wrap all `/admin/*` routes in an `is_admin` (or `role:admin`) middleware.
   - Wrap `/owner/*`, `/vet/*`, `/shelter/*` similarly by role.
   - Any authenticated user hitting a route outside their role → **redirect to a dedicated `403 Access Denied` page**, not a generic error or silent fail.
   - Unauthenticated user hitting any protected route → redirect to login.

3. **Access Denied page requirements**
   - Clear "You don't have permission to view this page" message.
   - Link back to the user's own dashboard (based on their actual role).
   - Do **not** reveal what's on the page they were denied (no leaking admin data structure).

4. **Data-level access control** (beyond routes):
   - A Veterinarian may only view a pet's health record if an Appointment exists linking that vet, that pet, and (implicitly) that owner.
   - A Shelter may only edit its own AdoptionListings (`shelter_id = auth user's id`).
   - A Pet Owner may only view/edit their own pets, health records, cart, and appointments (`owner_id = auth user's id`).
   - Admin bypasses ownership checks (full access) but every admin action should be logged (who did what, when — simple `activity_log` table, optional stretch goal).

5. **Verification gating**
   - Vet and Shelter accounts start as `status = pending`. They can log in and complete their profile but **cannot appear in public listings or accept bookings/listings until Admin sets `status = active`.**

6. **Password & session**
   - Passwords hashed (Laravel default: bcrypt/argon2 via the starter kit — no custom hashing needed).
   - Standard Laravel session/CSRF protection retained from the starter kit — don't bypass it for speed.

---

## 6. Tech Stack (decided)

- **Framework:** Laravel (starter kit already available — reuse its auth scaffolding, don't rebuild login/register from scratch).
- **Frontend:** Blade + Bootstrap (or Laravel's default starter-kit frontend) — fastest path given the 6-day window.
- **Database:** MySQL or PostgreSQL (per SRS options) — final pick recorded in `databaseschema.md`.
- **Maps:** Google Maps API or OpenStreetMap (for Contact Us + vet/shelter location features).

---

## 7. Companion Files

- **`FurShield_ CompleteDatabase .md`** — full table-by-table schema (31 tables) with SRS references and design decision comments. This is the source of truth for migrations.
- **`DECISIONS.md`** — records every architectural decision with rationale and SRS reference.
- **Migrations:** `database/migrations/2026_09_12_000001_*` through `2026_09_12_000026_*` (26 new migration files).
- **Seeders:** `RoleSeeder`, `SpeciesAndBreedsSeeder`, `CategorySeeder`, `FurShieldUsersSeeder`, `FurShieldDataSeeder`.

### Database Summary (31 Tables)

| # | Table | Purpose | SRS Reference |
|---|---|---|---|
| 1 | `users` | Single auth table for all 4 roles | Registration & Login |
| 2 | `roles` | Role definitions (admin, owner, vet, shelter) | RBAC |
| 3 | `role_user` | Many-to-many pivot | RBAC |
| 4 | `vet_profiles` | Vet-specific: qualification, clinic, verified | Vet Registration |
| 5 | `shelter_profiles` | Shelter-specific: name, city, coordinates, verified | Shelter Registration |
| 6 | `species` | Master data: Dog, Cat, Bird, etc. | Pet Profiles |
| 7 | `breeds` | Master data: Labrador, Persian, etc. | Pet Profiles |
| 8 | `specializations` | Master data: Surgery, Cardiology, etc. | Vet Specializations |
| 9 | `vet_specializations` | Many-to-many pivot | Vet Specializations |
| 10 | `pets` | Owner's pets with profile, health info | Pet Profiles |
| 11 | `pet_images` | Gallery images per pet | Pet Image Galleries |
| 12 | `health_records` | Vet visit records, diagnoses | Health Records |
| 13 | `vaccinations` | Vaccine history and due dates | Health Records |
| 14 | `medical_documents` | X-rays, lab reports, certificates | Health Records |
| 15 | `insurance_policies` | Pet insurance (view-only) | Insurance |
| 16 | `vet_availabilities` | Weekly schedule per vet | Vet Availability |
| 17 | `appointments` | Bookings between owners and vets | Appointment Booking |
| 18 | `treatments` | Diagnosis and treatment logs | Vet Treatment Logging |
| 19 | `prescriptions` | Medication details per treatment | Vet Treatment Logging |
| 20 | `categories` | Product categories (nested) | Product Browsing |
| 21 | `products` | Pet products with price, stock | Product Browsing |
| 22 | `product_images` | Gallery images per product | Product Browsing |
| 23 | `carts` | One cart per owner | Cart |
| 24 | `cart_items` | Products in cart with qty + price | Cart |
| 25 | `orders` | Order records (no payment) | Cart/Browse Only |
| 26 | `order_items` | Products per order | Cart/Browse Only |
| 27 | `adoption_listings` | Shelter pets for adoption | Adoption |
| 28 | `adoption_images` | Gallery per listing | Adoption |
| 29 | `adoption_applications` | Adoption interest forms | Adoption |
| 30 | `care_contents` | Articles, videos, FAQs | Care Options |
| 31 | `furshield_notifications` | In-app notifications | Notifications |
| 32 | `reviews` | Polymorphic reviews (vets, products, listings) | Ratings & Reviews |

**Starter kit tables kept:** `cache`, `jobs`, `sessions`, `password_reset_tokens`, `personal_access_tokens`, `media`, `settings`, `tags`, `taggables`, `activity_logs`, `contacts`, `subscribers`.

---

## 8. Seed Data

All passwords: `password`

| Role | Email | Status | Notes |
|---|---|---|---|
| Admin | `admin@furshield.com` | active | Full access |
| Owner | `sarah@example.com` | active | 2 pets (Buddy, Luna) |
| Owner | `mike@example.com` | active | 1 pet (Max) |
| Owner | `emily@example.com` | active | 1 pet (Milo) |
| Vet | `dr.carter@furshield.com` | active (verified) | General Practice, Surgery |
| Vet | `dr.chen@furshield.com` | active (verified) | Cardiology, Internal Medicine |
| Vet | `dr.hassan@furshield.com` | pending_verification | Dentistry — awaiting admin approval |
| Shelter | `info@happypaws.com` | active (verified) | Happy Paws, capacity 50 |
| Shelter | `contact@newbeginnings.com` | pending_verification | New Beginnings — awaiting admin approval |

---

## 9. Six-Day Build Plan (Updated)

| Day | Focus | Status |
|---|---|---|
| Day 1 | Laravel starter kit + RBAC + access-denied page + role-based auth + seed roles | **DONE** |
| Day 2 | Pet Owner module: pet profiles, health records, vaccinations, medical documents | Migration files created |
| Day 3 | Vet module: profile, verification-gated listing, medical history view, treatment logging, appointments | Migration files created |
| Day 4 | Shelter module: adoption listings, adoption applications, care status, admin verification flow | Migration files created |
| Day 5 | Admin: Products/Categories CRUD + Cart + Orders + common features (search/filter, notifications, ratings) | Migration files created |
| Day 6 | Testing, seed test data, record demo video, write documentation, package submission zip | Seeders created |

---

*Compiled from the official SRS (Aptech Limited / TechWiz 6) plus team decisions. All architectural decisions documented in `DECISIONS.md`.*
