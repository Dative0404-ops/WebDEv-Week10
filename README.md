# WebDev Week 10 — Campus Events (Laravel + PostgreSQL)

A small **Campus Events** data layer built with Laravel 12 and PostgreSQL 18.
Venues host events, participants register for events, and notes can be attached
to either an event or a venue with a single polymorphic table.

---

## 1. The data rules

| Link | What the system needs |
|---|---|
| Venue and Event | A venue hosts many events. An event happens at one venue. A venue that still has events must **not** be deletable. |
| Event and Participant | An event has many participants. A participant joins many events. Each registration stores a **status** (`registered` / `waitlisted`). The same participant cannot register for the same event twice. If an event or participant is deleted, their registrations disappear too. |
| Note | A note belongs to **either** an event **or** a venue, using one `notes` table. |

---

## 2. ERD (text form)

```
venues                          events
------                          ------
id (PK)  ───────1:N──────────►  id (PK)
name                            venue_id (FK → venues.id, ON DELETE RESTRICT)
address                         title
capacity                        description
                                 starts_at
        │                              │
        │ (polymorphic)                │ (polymorphic)
        │                              │
        └──────────┐        ┌──────────┘
                   ▼        ▼
                    notes
                    -----
                    id (PK)
                    body
                    notable_id      ← points at an event OR a venue (no FK possible)
                    notable_type    ← "App\Models\Event" or "App\Models\Venue"


events                          participants
------                          ------------
id (PK)  ────┐            ┌──── id (PK)
             │            │     name
             │            │     email (UNIQUE)
             ▼            ▼
        event_participant            (the registration / pivot table)
        -----------------
        id (PK)
        event_id        (FK → events.id,       ON DELETE CASCADE)
        participant_id  (FK → participants.id, ON DELETE CASCADE)
        status          ('registered' | 'waitlisted')
        UNIQUE (event_id, participant_id)
```

---

## 3. Relationship table

| Link | Type | Model that holds the foreign key | Relationship methods | ON DELETE | Why this choice |
|---|---|---|---|---|---|
| Venue ↔ Event | One-to-many (1:N) | **Event** (`events.venue_id`) | `Venue::events()` (hasMany) / `Event::venue()` (belongsTo) | **RESTRICT** | A venue that still has events must not be deletable — the database refuses to delete a parent that is still referenced. |
| Event ↔ Participant | Many-to-many (N:N) | **EventParticipant pivot** (`event_participant`) | `Event::participants()` / `Participant::events()` (both belongsToMany) | **CASCADE** | If an event or a participant is deleted, their registration rows must disappear too. |
| Note → Event / Venue | Polymorphic (1:N + 1:N) | **Note** (`notes.notable_id` + `notes.notable_type`) | `Event::notes()` / `Venue::notes()` (morphMany), `Note::notable()` (morphTo) | **none (no FK)** | `notable_id` can point at two different tables, so PostgreSQL cannot enforce a single reference — the database allows an orphan note; only our code can prevent it. |

Extra rule enforced by the database: the composite **`UNIQUE (event_id, participant_id)`** on the pivot stops the same participant registering for the same event twice.

---

## 4. Tables created

| Table | Purpose | Key columns |
|---|---|---|
| `venues` | Places that host events | `id` (PK), `name`, `address`, `capacity` |
| `events` | Events at a venue | `id` (PK), `venue_id` (FK, RESTRICT), `title`, `description`, `starts_at` |
| `participants` | People who join events | `id` (PK), `name`, `email` (UNIQUE) |
| `event_participant` | Registrations (pivot) | `id` (PK), `event_id` (FK, CASCADE), `participant_id` (FK, CASCADE), `status`, UNIQUE(event_id, participant_id) |
| `notes` | Notes on an event or a venue | `id` (PK), `body`, `notable_id`, `notable_type` |

---

## 5. Setup

```bash
# 1) install dependencies
composer install

# 2) copy the environment file and set your PostgreSQL credentials
copy .env.example .env
php artisan key:generate
#   edit .env -> DB_CONNECTION=pgsql, DB_DATABASE=campus_events,
#                DB_USERNAME, DB_PASSWORD

# 3) create the database (once)
createdb campus_events

# 4) build the schema and the dataset
php artisan migrate:fresh --seed

# 5) browse the app
php artisan serve
#   http://127.0.0.1:8000/            -> home dashboard
#   http://127.0.0.1:8000/report      -> the seven required queries
```

The `.env` file is **not** committed (it may hold real credentials).
`.env.example` shows the same keys with an empty password.

---

## 6. Seeded dataset

`php artisan migrate:fresh --seed` produces:

- **3** venues
- **6** events (2 per venue)
- **8** participants
- **12** registrations (both `registered` and `waitlisted` used)
- **6** notes (3 on events, 3 on venues)

Rows are created **through the relationships** (`$venue->events()->create(...)`,
`$event->participants()->attach(...)`, `$event->notes()->create(...)`), so the
seeder doubles as a test of the relationships.

---

## 7. What PostgreSQL enforces vs. what our code must enforce

**Enforced by PostgreSQL (see the three error screenshots):**

1. `events.venue_id` → inserting an event with a non-existent venue fails (FK).
2. `events.venue_id` `ON DELETE RESTRICT` → deleting a venue that still has events fails.
3. `UNIQUE (event_id, participant_id)` → registering the same participant twice for the same event fails.

**NOT enforced by PostgreSQL:**

4. A note whose `notable_id` points at a missing event/venue is **accepted**. PostgreSQL cannot stop this because `notable_id` may reference *two* different tables (`events` or `venues`), and a single foreign key can only point at one table. Keeping orphan notes out is a job for our application code.

---

## 8. The seven report queries

Implemented in `app/Http/Controllers/ReportController.php`, shown on `/report`:

1. Every venue with the number of events it hosts — `Venue::withCount('events')`
2. All events at one chosen venue — `$venue->events()`
3. All participants of one chosen event, with registration status — `$event->participants()` (pivot `status`)
4. All events one chosen participant has joined — `$participant->events()`
5. All events that have at least one waitlisted participant — `Event::whereHas('participants', ...)`
6. All notes of one chosen event, and all notes of one chosen venue — `$event->notes()`, `$venue->notes()`
7. For one chosen note, the name and kind of the thing it belongs to — `$note->notable` (morphTo)
