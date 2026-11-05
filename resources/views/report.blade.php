@extends('layouts.app')

@section('title', 'Report · PUP Campus Events')

@section('content')
    <h1 class="page-title">Relationship Report</h1>
    <p class="page-sub">
        Results of the seven required queries. Every query is written with Eloquent
        relationships &mdash; no hand-written JOINs.
    </p>

    {{-- QUERY 1 --}}
    <div class="card">
        <h2><span class="qnum">Query 1</span>Every venue with the number of events it hosts</h2>
        <p class="hint">Venue hasMany Event &middot; <code>Venue::withCount('events')</code></p>
        <table>
            <thead>
                <tr><th>#</th><th>Venue</th><th>Events it hosts</th></tr>
            </thead>
            <tbody>
                @foreach ($venuesWithEventCounts as $venue)
                    <tr>
                        <td>{{ $venue->id }}</td>
                        <td>{{ $venue->name }}</td>
                        <td>{{ $venue->events_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- QUERY 2 --}}
    <div class="card">
        <h2><span class="qnum">Query 2</span>All events at one chosen venue</h2>
        <p class="hint">Venue hasMany Event &middot; <code>$chosenVenue-&gt;events()</code></p>
        <p class="callout">
            Chosen venue: <strong>{{ $chosenVenue?->name ?? '—' }}</strong>
            (id {{ $chosenVenue?->id }})
        </p>
        <table>
            <thead>
                <tr><th>#</th><th>Event</th><th>Starts at</th></tr>
            </thead>
            <tbody>
                @forelse ($eventsAtChosenVenue as $event)
                    <tr>
                        <td>{{ $event->id }}</td>
                        <td>{{ $event->title }}</td>
                        <td>{{ $event->starts_at?->format('M d, Y h:i A') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="empty">No events found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- QUERY 3 --}}
    <div class="card">
        <h2><span class="qnum">Query 3</span>All participants of one chosen event, with their registration status</h2>
        <p class="hint">Event belongsToMany Participant &middot; <code>$chosenEvent-&gt;participants()</code>, status read from <code>$participant-&gt;pivot-&gt;status</code></p>
        <p class="callout">
            Chosen event: <strong>{{ $chosenEvent?->title ?? '—' }}</strong>
            (id {{ $chosenEvent?->id }})
        </p>
        <table>
            <thead>
                <tr><th>#</th><th>Participant</th><th>Email</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse ($chosenEventParticipants as $participant)
                    <tr>
                        <td>{{ $participant->id }}</td>
                        <td>{{ $participant->name }}</td>
                        <td>{{ $participant->email }}</td>
                        <td><span class="badge {{ $participant->pivot->status }}">{{ $participant->pivot->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">No participants found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- QUERY 4 --}}
    <div class="card">
        <h2><span class="qnum">Query 4</span>All events one chosen participant has joined</h2>
        <p class="hint">Participant belongsToMany Event &middot; <code>$chosenParticipant-&gt;events()</code></p>
        <p class="callout">
            Chosen participant: <strong>{{ $chosenParticipant?->name ?? '—' }}</strong>
            (id {{ $chosenParticipant?->id }})
        </p>
        <table>
            <thead>
                <tr><th>#</th><th>Event</th><th>Starts at</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse ($chosenParticipantEvents as $event)
                    <tr>
                        <td>{{ $event->id }}</td>
                        <td>{{ $event->title }}</td>
                        <td>{{ $event->starts_at?->format('M d, Y h:i A') }}</td>
                        <td><span class="badge {{ $event->pivot->status }}">{{ $event->pivot->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="empty">No events found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- QUERY 5 --}}
    <div class="card">
        <h2><span class="qnum">Query 5</span>All events that have at least one waitlisted participant</h2>
        <p class="hint">Event whereHas Participant with pivot status &middot; <code>Event::whereHas('participants', ...)</code></p>
        <table>
            <thead>
                <tr><th>#</th><th>Event</th><th>Total participants</th></tr>
            </thead>
            <tbody>
                @forelse ($eventsWithWaitlist as $event)
                    <tr>
                        <td>{{ $event->id }}</td>
                        <td>{{ $event->title }}</td>
                        <td>{{ $event->participants_count }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="empty">No events with a waitlist.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- QUERY 6 --}}
    <div class="card">
        <h2><span class="qnum">Query 6</span>All notes of one chosen event, and all notes of one chosen venue</h2>
        <p class="hint">Polymorphic note &middot; <code>$chosenEvent-&gt;notes()</code> and <code>$chosenVenue-&gt;notes()</code> share ONE notes table</p>

        <p class="callout">
            Notes for the chosen event: <strong>{{ $chosenEvent?->title ?? '—' }}</strong>
        </p>
        <table>
            <thead>
                <tr><th>#</th><th>Note</th></tr>
            </thead>
            <tbody>
                @forelse ($chosenEventNotes as $note)
                    <tr><td>{{ $note->id }}</td><td>{{ $note->body }}</td></tr>
                @empty
                    <tr><td colspan="2" class="empty">No notes.</td></tr>
                @endforelse
            </tbody>
        </table>

        <p class="callout" style="margin-top:16px;">
            Notes for the chosen venue: <strong>{{ $chosenVenue?->name ?? '—' }}</strong>
        </p>
        <table>
            <thead>
                <tr><th>#</th><th>Note</th></tr>
            </thead>
            <tbody>
                @forelse ($chosenVenueNotes as $note)
                    <tr><td>{{ $note->id }}</td><td>{{ $note->body }}</td></tr>
                @empty
                    <tr><td colspan="2" class="empty">No notes.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- QUERY 7 --}}
    <div class="card">
        <h2><span class="qnum">Query 7</span>For one chosen note: the name and the kind of the thing it belongs to</h2>
        <p class="hint">Polymorphic inverse &middot; <code>$chosenNote-&gt;notable</code> (morphTo)</p>
        @if ($chosenNote)
            <table>
                <tbody>
                    <tr><th>Note</th><td>{{ $chosenNote->body }}</td></tr>
                    <tr><th>Belongs to</th><td>{{ $notableName }}</td></tr>
                    <tr><th>Kind of thing</th><td><span class="badge type">{{ $notableType }}</span></td></tr>
                </tbody>
            </table>
        @else
            <p class="empty">No notes found.</p>
        @endif
    </div>
@endsection
