<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Participant;
use App\Models\Venue;
use Illuminate\Database\Seeder;

/**
 * Builds the whole Campus Events dataset using the Eloquent RELATIONSHIPS
 * (not raw inserts), so the seeder also proves the links work.
 *
 * Targets from the exercise:
 *   - at least 3 venues          -> 3
 *   - at least 6 events          -> 6
 *   - at least 8 participants    -> 8
 *   - at least 12 registrations  -> 12 (both "registered" and "waitlisted" used)
 *   - at least 6 notes           -> 6 (3 on events, 3 on venues)
 */
class CampusEventSeeder extends Seeder
{
    public function run(): void
    {
        // ---- 1) Three venues -------------------------------------------------
        $venues = collect([
            ['name' => 'Grand Auditorium',  'address' => 'PUP Main Campus, Sta. Mesa', 'capacity' => 1200],
            ['name' => 'Engineering Hall',  'address' => 'PUP Main Campus, Sta. Mesa', 'capacity' => 400],
            ['name' => 'Open Field',        'address' => 'PUP Main Campus, Sta. Mesa', 'capacity' => 3000],
        ])->map(fn (array $row) => Venue::create($row));

        // ---- 2) Six events, created THROUGH the venue->events relationship ----
        // Each row is: [venue index, title, description, starts_at]
        $eventRows = [
            [0, 'Freshman Orientation',  'Welcome program for incoming freshmen.',        '2026-11-10 08:00:00'],
            [0, 'Leadership Summit',     'Talks and workshops for student leaders.',       '2026-11-18 09:00:00'],
            [1, 'Coding Bootcamp',       'Hands-on intro to web development.',             '2026-11-12 13:00:00'],
            [1, 'Robotics Expo',         'Student robots on display and in competition.',  '2026-11-20 10:00:00'],
            [2, 'Intramurals Opening',   'Parade and opening ceremony of intramurals.',    '2026-11-15 07:00:00'],
            [2, 'Acoustic Night',        'Live acoustic performances under the stars.',    '2026-11-25 18:00:00'],
        ];

        $events = collect($eventRows)->map(function (array $row) use ($venues) {
            [$venueIndex, $title, $description, $startsAt] = $row;

            // $venues[$venueIndex]->events() is the hasMany side: Laravel fills
            // in venue_id for us.
            return $venues[$venueIndex]->events()->create([
                'title'       => $title,
                'description' => $description,
                'starts_at'   => $startsAt,
            ]);
        });

        // ---- 3) Eight participants -------------------------------------------
        $participants = collect([
            ['name' => 'Maria Santos',   'email' => 'maria.santos@example.com'],
            ['name' => 'Juan Dela Cruz', 'email' => 'juan.delacruz@example.com'],
            ['name' => 'Ana Reyes',      'email' => 'ana.reyes@example.com'],
            ['name' => 'Mark Bautista',  'email' => 'mark.bautista@example.com'],
            ['name' => 'Liza Ramos',     'email' => 'liza.ramos@example.com'],
            ['name' => 'Paolo Garcia',   'email' => 'paolo.garcia@example.com'],
            ['name' => 'Grace Lim',      'email' => 'grace.lim@example.com'],
            ['name' => 'Ramon Aquino',   'email' => 'ramon.aquino@example.com'],
        ])->map(fn (array $row) => Participant::create($row));

        // ---- 4) Twelve registrations through the many-to-many relationship ----
        // Each row is: [event index, participant index, status].
        // No pair (event, participant) repeats, because the database's UNIQUE
        // constraint would reject a duplicate anyway.
        $registrationRows = [
            [0, 0, 'registered'], [0, 1, 'registered'], [0, 2, 'waitlisted'],
            [1, 0, 'registered'], [1, 3, 'registered'], [1, 4, 'waitlisted'],
            [2, 1, 'registered'], [2, 5, 'waitlisted'],
            [3, 6, 'registered'],
            [4, 7, 'registered'],
            [5, 2, 'registered'], [5, 0, 'waitlisted'],
        ];

        foreach ($registrationRows as [$eventIndex, $participantIndex, $status]) {
            // attach() writes a row into event_participant, including the extra
            // "status" pivot value.
            $events[$eventIndex]->participants()->attach($participants[$participantIndex]->id, [
                'status' => $status,
            ]);
        }

        // ---- 5) Six notes through the polymorphic morphMany relationship -----
        // Three notes belong to events...
        $events[0]->notes()->create(['body' => 'Bring your own water bottle.']);
        $events[0]->notes()->create(['body' => 'Doors open 30 minutes early.']);
        $events[2]->notes()->create(['body' => 'Laptops are provided; just bring yourself.']);

        // ...and three notes belong to venues (same notes table, different type).
        $venues[0]->notes()->create(['body' => 'The auditorium is fully air-conditioned.']);
        $venues[0]->notes()->create(['body' => 'Wheelchair-accessible entrance at the north wing.']);
        $venues[2]->notes()->create(['body' => 'The field gets muddy after heavy rain.']);
    }
}
