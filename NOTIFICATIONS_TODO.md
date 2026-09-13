# Notifications — To-Do List

> Store this for later implementation. Not needed for competition demo.

## Currently Working
| Notification | When | Channels | Status |
|---|---|---|---|
| Contact Form Message | Someone submits /contact | Mail + Database | ✅ Working |
| Appointment Confirmed | Seeded demo data | Database | ⚠️ Seeded only |
| Vaccination Reminder | Seeded demo data | Database | ⚠️ Seeded only |

## Not Implemented (Need to Build)
| Notification | When | Recipient | Priority |
|---|---|---|---|
| New review received | Owner leaves a review | Vet / Shelter | Medium |
| Appointment requested | Owner books appointment | Vet | High |
| Appointment status changed | Vet approves/cancels | Owner | High |
| Verification approved | Admin approves vet/shelter | Vet / Shelter | High |
| Verification rejected | Admin rejects vet/shelter | Vet / Shelter | High |
| Order placed | Owner purchases product | Admin | Medium |
| New adoption application | Someone applies to adopt | Shelter | Medium |

## Existing Code
- `app/Notifications/ContactFormNotification.php` — mail + database, type: `contact_message`
- `app/Notifications/WelcomeNotification.php` — queued, never dispatched
- `app/Models/FurshieldNotification.php` — custom notification model (user_id, title, message, type, link, is_read)
- `app/Http/Controllers/Admin/NotificationController.php` — index, markAsRead, markAllAsRead, destroy
