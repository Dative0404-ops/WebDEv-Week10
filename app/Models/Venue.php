<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Venue = a place that hosts events.
 *
 * Two links start from this model:
 *   - events() : one-to-many  (a venue hosts many events; the foreign key
 *                venue_id lives on the events table)
 *   - notes()  : polymorphic  (a venue can have many notes, stored in the
 *                shared notes table through notable_type/notable_id)
 */
class Venue extends Model
{
    use HasFactory;

    // Columns we allow Laravel to fill in "mass assignment" (create/update).
    protected $fillable = [
        'name',
        'address',
        'capacity',
    ];

    /**
     * One-to-many: THIS venue hosts MANY events.
     * The "many" side (events) holds the foreign key venue_id.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Polymorphic: THIS venue owns MANY notes.
     * morphMany() will store notable_type = App\Models\Venue on each note.
     */
    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'notable');
    }
}
