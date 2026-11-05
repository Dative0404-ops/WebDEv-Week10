<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Note;
use App\Models\Participant;
use App\Models\Venue;

/**
 * The only controller of the app. It serves two pages:
 *   - home()  : a friendly dashboard overview
 *   - index() : the assignment report with the seven required queries
 *
 * Every query below uses ELOQUENT RELATIONSHIPS (no hand-written JOIN).
 */
class ReportController extends Controller
{
    /**
     * Home dashboard: venues (with event counts), events and participants.
     */
    public function home()
    {
        // withCount('events') adds an "events_count" attribute to each venue.
        $venues       = Venue::withCount('events')->orderBy('name')->get();
        $events       = Event::with('venue')->orderBy('starts_at')->get();
        $participants = Participant::withCount('events')->orderBy('name')->get();

        return view('home', compact('venues', 'events', 'participants'));
    }

    /**
     * The report page: runs the seven required queries and passes the
     * results to the Blade view, where each one is clearly labeled.
     */
    public function index()
    {
        // QUERY 1: Every venue with the number of events it hosts.
        // withCount() lets the database COUNT related events per venue.
        $venuesWithEventCounts = Venue::withCount('events')->orderBy('name')->get();

        // The "chosen" rows used by queries 2 to 7 (also shown on the page).
        $chosenVenue       = Venue::orderBy('id')->first();
        $chosenEvent       = Event::orderBy('id')->first();
        $chosenParticipant = Participant::orderBy('id')->first();
        $chosenNote        = Note::orderBy('id')->first();

        // QUERY 2: All events at one chosen venue (Venue hasMany Events).
        $eventsAtChosenVenue = $chosenVenue
            ? $chosenVenue->events()->orderBy('starts_at')->get()
            : collect();

        // QUERY 3: All participants of one chosen event, with the status of
        // each registration (read from the pivot: $participant->pivot->status).
        $chosenEventParticipants = $chosenEvent
            ? $chosenEvent->participants()->orderBy('name')->get()
            : collect();

        // QUERY 4: All events one chosen participant has joined.
        $chosenParticipantEvents = $chosenParticipant
            ? $chosenParticipant->events()->orderBy('starts_at')->get()
            : collect();

        // QUERY 5: All events that have at least one waitlisted participant.
        // whereHas() keeps only events whose participants include a waitlisted
        // registration; the pivot column is qualified with its table name.
        $eventsWithWaitlist = Event::whereHas('participants', function ($query) {
                $query->where('event_participant.status', 'waitlisted');
            })
            ->withCount('participants')
            ->orderBy('title')
            ->get();

        // QUERY 6: All notes of one chosen event AND all notes of one chosen venue.
        $chosenEventNotes = $chosenEvent ? $chosenEvent->notes()->get() : collect();
        $chosenVenueNotes = $chosenVenue ? $chosenVenue->notes()->get() : collect();

        // QUERY 7: For one chosen note, the NAME and the KIND of the thing it
        // belongs to. morphTo() resolves "notable" to an Event OR a Venue.
        $notableName = null;
        $notableType = null;

        if ($chosenNote && $chosenNote->notable) {
            $notable     = $chosenNote->notable;                 // the related model
            $notableType = class_basename($notable);             // "Event" or "Venue"
            $notableName = $notable->title ?? $notable->name;    // Event uses title, Venue uses name
        }

        return view('report', compact(
            'venuesWithEventCounts',
            'chosenVenue', 'eventsAtChosenVenue',
            'chosenEvent', 'chosenEventParticipants',
            'chosenParticipant', 'chosenParticipantEvents',
            'eventsWithWaitlist',
            'chosenEventNotes', 'chosenVenueNotes',
            'chosenNote', 'notableName', 'notableType',
        ));
    }
}
