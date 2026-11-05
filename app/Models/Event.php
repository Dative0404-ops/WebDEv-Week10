<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Event = something that happens at one venue and that participants join.
 *
 * Three links are declared here:
 *   - venue()        : the "one" side of venue -> events (belongsTo)
 *   - participants() : many-to-many with Participant via the event_participant pivot
 *   - notes()        : polymorphic notes attached to an event
 */
class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'venue_id',
        'title',
        'description',
        'starts_at',
    ];

    // Cast stored values into rich types automatically.
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
        ];
    }

    /**
     * One-to-many (inverse): THIS event happens at ONE venue.
     * The foreign key venue_id lives on this (events) table.
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Many-to-many: THIS event has MANY participants, and each participant
     * can join MANY events. The link table is event_participant.
     * withPivot('status') lets us read the extra "status" column of a registration.
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Participant::class)
                    ->withPivot('status') // expose the pivot column on each related row
                    ->withTimestamps();   // keep the pivot's created_at/updated_at filled
    }

    /**
     * Polymorphic: THIS event owns MANY notes.
     * morphMany() stores notable_type = App\Models\Event on each note.
     */
    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable');
    }
}
