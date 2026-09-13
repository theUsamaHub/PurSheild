# FurShield — Roles & Functional Requirements

> Each role's capabilities, data access rules, and workflow.
> SRS Reference: Functional Requirements by Role (Section 3).

---

## Pet Owner

**Core Flow:** Register → Add pets → Book vet appointments → Browse products

### Registration & Login
- Register with name, email, phone, address.
- Redirected to `/owner/dashboard` after login.
- Can have multiple pets.
- Optional family account sharing (SRS).

### Manage Pet Profiles (SRS)
- Add/edit/view/delete pets: name, species, breed, DOB, weight, color, gender, profile image.
- Multiple pets per owner — tabbed UI.
- Optional microchip number, neuter status.
- Pet gallery: multiple images per pet.

### Track Health Records (SRS)
- View vaccination history, treatment records, medical documents per pet.
- Visual timeline of vaccinations/treatments/milestones.
- Upload/store vet certificates, X-rays, lab reports.
- Upload/store/view insurance policy details (view-only, no claims processing per SRS).

### View/Purchase Products (SRS)
- Browse product categories with filters.
- View product details (images, price, description, stock status).
- Add/remove/modify cart items.
- Place order (informational only — no payment gateway per SRS constraint).

### Access Care Options (SRS)
- Categorized care content: articles, videos, FAQs.
- Topics: feeding, hygiene, exercise, health, training.

### Appointment Booking (SRS)
- Browse listed vets (verified only).
- See vet specializations, consultation fees, availability, ratings.
- Auto-suggest vets based on pet condition or location.
- Book appointment with reason.
- Track status: pending → approved → completed/cancelled.
- Receive notifications on appointment changes.

### Ratings & Reviews (SRS)
- Rate and review vets (1-5 stars + comment).
- One review per vet per owner (application-level constraint).

### Common Features
- Search/sort/filter across pets, products, care articles, vets.
- Responsive design — mobile-friendly.
- Receive notifications: vaccination reminders, appointment updates, product alerts.
- Profile: edit name, email, phone, address, profile image.

---

## Veterinarian

**Core Flow:** Register → Complete profile → Await admin verification → Accept appointments → Log treatments

### Registration & Login (SRS)
- Register with name, email, phone, address.
- Account starts as `status = pending_verification`.

### Complete Profile (SRS)
- Post-login: add qualification, experience years, available time slots.
- Add clinic name, clinic address, bio.
- Set consultation fee (displayed to owners).
- Select specializations (many-to-many from admin-managed list).

### Verification Gating (Our Addition)
- Cannot appear in public vet listings until Admin sets `is_verified = true`.
- Cannot accept bookings until verified.
- Can log in and edit profile while pending.

### Manage Appointments (SRS)
- View upcoming appointment requests.
- Approve, reject, or reschedule appointments.
- Update availability schedule (weekly: day × time slots).
- Toggle specific days on/off.

### Access Pet Medical History (SRS)
- Can ONLY view medical history of pets whose owners booked an appointment with them.
- Data-level access control: `Appointment` record must exist linking vet → pet → owner.
- Structured view: symptoms, past treatments, lab results, prescriptions.

### Log Treatments (SRS)
- After appointment: record symptoms, diagnosis, treatment given, follow-up date, notes.
- Write prescriptions: medicine name, dosage, frequency, duration, instructions.
- Multiple prescriptions per treatment.

### Common Features
- Get notified: new/changed appointment requests.
- Receive reviews from pet owners (1-5 stars).
- Profile: edit shared fields (name, email, phone, photo) + vet-specific fields (qualification, clinic, availability).

---

## Animal Shelter

**Core Flow:** Register → Complete profile → Await admin verification → List pets for adoption → Coordinate with adopters

### Registration & Login (SRS)
- Register with shelter name, contact person, email, contact number, address.
- Account starts as `status = pending_verification`.

### Complete Profile
- Add shelter name, description, city, website, contact number.
- Set capacity (max animals).
- Add location coordinates (latitude/longitude) for map-based search.

### Verification Gating (SRS)
- SRS explicitly requires "a verified account."
- Admin performs verification — shelter cannot create adoption listings until verified.
- Cannot appear in public shelter listings until verified.

### List Adoptable Pets (SRS)
- Create listings: pet name, species, breed, age, gender, health status, description.
- Multiple images per listing (gallery).
- Status: available → pending → adopted / inactive.

### Manage Listings
- Edit listing details.
- Update status when adoption progresses.
- Deactivate listings when pet is adopted.

### Coordinate with Adopters (SRS)
- Receive adoption interest forms (applications) from pet owners.
- View applicant message.
- Approve or reject with a response message.
- Finalize adoption — mark listing as "adopted."
- Notify adopter via notification.

### Update Pet Care Status (SRS)
- Log feeding, grooming, medical notes per animal in shelter care.

### Common Features
- Get notified: new adoption applications, application status changes.
- Receive reviews from users.
- Profile: edit shared fields (name, email, phone, photo) + shelter-specific fields (description, city, website, capacity).

---

## Admin

**Core Flow:** Manage everything — users, vets, shelters, products, content, system

> **Justification:** The SRS describes Pet Owners browsing/purchasing products but never says who creates, edits, prices, or removes products/categories/stock. Without Admin: no one owns the catalog, no one can verify vets/shelters, no one moderates reviews, no user management. Admin closes these SRS gaps.

### Dashboard
- System-wide stats: total owners, pets, vets, shelters, appointments, orders.
- Pending verifications count.
- Recent activity.

### Verify Vets & Shelters (Our Addition)
- Review pending vet/shelter accounts.
- Approve: set `is_verified = true`, `verified_at`, `verified_by`, `status = active`.
- Reject: keep `status = pending_verification` with reason.
- Vet/shelter cannot appear in public listings until verified.

### Manage Users
- View all users (all roles).
- Disable/enable accounts (`status = active/inactive`).
- Suspend accounts with reason (`suspended_at`, `suspension_reason`).
- Self-deletion blocked (prevents admin from locking themselves out).

### Manage Products & Categories (SRS)
- Full CRUD for products: create, edit, delete, set price, stock, featured flag, images.
- Full CRUD for categories: nested categories, reorder, enable/disable.
- Manages stock levels, pricing — the missing piece that makes "View/Purchase Products" work.

### Moderate Ratings & Reviews (SRS)
- Remove abusive reviews.
- View all reviews across vets, shelters, products.

### Manage Species & Breeds
- Add/edit/delete species (Dog, Cat, Bird, etc.).
- Add/edit/delete breeds per species.
- Enable/disable without deleting.

### Manage Specializations
- Add/edit/delete vet specializations (Surgery, Cardiology, etc.).

### Manage Care Articles/FAQs (SRS)
- Create/edit/delete care content: articles, videos, FAQs.
- Categories: feeding, hygiene, exercise, health, training.
- Publish/unpublish without deleting.

### Manage Static Content (SRS)
- About Us, Contact Us pages.
- Google Maps embed for Contact Us.

### User Management
- View all users with role, status, last login.
- Enable/disable accounts.
- Reset access (re-enable suspended accounts).

### System Management
- **Settings:** DB-backed grouped settings (General, SEO, Social, Mail).
- **Activity Logs:** View who did what and when. Export to CSV.
- **Notifications:** Send system notifications to users.
- **Backups:** Create/download/delete PostgreSQL database backups.
- **Logs:** View application logs in real-time. Clear/download.
- **Maintenance Mode:** Toggle with custom message and bypass routes.
- **IP Restrictions:** Manage IP whitelist for admin panel (wildcard/CIDR support).
- **Sessions:** View/revoke active user sessions.
- **Health Dashboard:** DB, cache, queue, disk, PHP/Laravel version checks.

### Profile
- Edit shared fields (name, email, phone, photo).

---

## Access Control Rules

### Route-Level (Middleware)

| Route Prefix | Owner | Vet | Shelter | Admin |
|---|---|---|---|---|
| `/owner/*` | Yes | No | No | Yes (bypass) |
| `/vet/*` | No | Yes | No | Yes (bypass) |
| `/shelter/*` | No | No | Yes | Yes (bypass) |
| `/admin/*` | No | No | No | Yes |
| `/browse/products` | Yes | Yes | Yes | Yes |
| `/browse/vets` | Yes | No | No | Yes |
| `/browse/adoption` | Yes | No | No | Yes |

### Data-Level (Beyond Routes)

| Rule | Enforcement |
|---|---|
| Vet can only view pet health records if an Appointment exists linking that vet, that pet, and that owner | `whereHas('appointments', fn($q) => $q->where('vet_id', auth()->id()))` |
| Shelter can only edit its own AdoptionListings | `shelter_id = auth()->id` |
| Owner can only view/edit their own pets, health records, cart, appointments | `owner_id = auth()->id` |
| Admin bypasses ownership checks | Full access, but every admin action is logged |
| Unauthenticated user hitting protected route | Redirect to login |
| Authenticated user hitting wrong-role route | Redirect to dedicated 403 Access Denied page |

### 403 Access Denied Page Requirements
- Clear "You don't have permission to view this page" message.
- Link back to the user's own dashboard (based on their actual role).
- Do NOT reveal what's on the page they were denied.

### Verification Gating
- Vet and Shelter accounts start as `status = pending_verification`.
- They can log in and complete their profile.
- They CANNOT appear in public listings or accept bookings/listings.
- Admin sets `status = active` and `is_verified = true` after review.

---

## Notification Matrix

| Event | Recipients | Type |
|---|---|---|
| New appointment request | Vet | `appointment` |
| Appointment approved/rejected | Owner | `appointment` |
| Appointment rescheduled | Owner, Vet | `appointment` |
| Vaccination due in 7 days | Owner | `vaccination` |
| Follow-up reminder | Owner | `appointment` |
| New adoption application | Shelter | `adoption` |
| Application approved/rejected | Owner (applicant) | `adoption` |
| Order placed | Owner | `order` |
| New pending verification | Admin | `verification` |
| Account suspended | Affected user | `system` |
| New review received | Vet / Shelter | `review` |

---

*Last updated: September 12, 2026*
