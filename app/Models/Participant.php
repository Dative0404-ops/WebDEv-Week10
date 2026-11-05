<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Participant = a person who can register for many events.
 *
 * One link starts here:
 *   - events() : many-to-many with Event via the event_participant pivot.
 *     This is the OTHER side of Event::participants().
 */
class Participant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
    ];

    /**
     * Many-to-many: THIS participant joins MANY events.
     * withPivot('status') lets us read this person's status for each event
     * ("registered" or "waitlisted").
     */
    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)
                    ->withPivot('status') // expose the registration status
                    ->withTimestamps();   // keep the pivot timestamps filled
    }
}
