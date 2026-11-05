@extends('layouts.app')

@section('title', 'Home · PUP Campus Events')

@section('content')
    <h1 class="page-title">Campus Events Overview</h1>
    <p class="page-sub">A quick look at the data our relational model holds.</p>

    <div class="stats">
        <div class="stat">
            <div class="n">{{ $venues->count() }}</div>
            <div class="l">Venues</div>
        </div>
        <div class="stat">
            <div class="n">{{ $events->count() }}</div>
            <div class="l">Events</div>
        </div>
        <div class="stat">
            <div class="n">{{ $participants->count() }}</div>
            <div class="l">Participants</div>
        </div>
    </div>

    {{-- Venues with the number of events they host --}}
    <div class="card">
        <h2>Venues and how many events they host</h2>
        <p class="hint">Uses the <code>Venue::withCount('events')</code> relationship query.</p>
        <table>
            <thead>
                <tr><th>Venue</th><th>Address</th><th>Capacity</th><th>Events</th></tr>
            </thead>
            <tbody>
                @foreach ($venues as $venue)
                    <tr>
                        <td>{{ $venue->name }}</td>
                        <td>{{ $venue->address }}</td>
                        <td>{{ number_format($venue->capacity) }}</td>
                        <td>{{ $venue->events_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Events with their venue --}}
    <div class="card">
        <h2>Events</h2>
        <p class="hint">Each row goes through the <code>Event::venue()</code> belongsTo relationship.</p>
        <table>
            <thead>
                <tr><th>Event</th><th>Venue</th><th>Starts at</th></tr>
            </thead>
            <tbody>
                @foreach ($events as $event)
                    <tr>
                        <td>{{ $event->title }}</td>
                        <td>{{ $event->venue->name }}</td>
                        <td>{{ $event->starts_at?->format('M d, Y h:i A') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Participants with how many events they joined --}}
    <div class="card">
        <h2>Participants</h2>
        <p class="hint">Uses the <code>Participant::withCount('events')</code> relationship query.</p>
        <table>
            <thead>
                <tr><th>Name</th><th>Email</th><th>Events joined</th></tr>
            </thead>
            <tbody>
                @foreach ($participants as $participant)
                    <tr>
                        <td>{{ $participant->name }}</td>
                        <td>{{ $participant->email }}</td>
                        <td>{{ $participant->events_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="page-sub">Open the <a href="{{ route('report') }}">Report</a> page to see the seven required queries.</p>
@endsection
