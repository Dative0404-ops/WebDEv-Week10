<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Note = a piece of text attached to EITHER an event OR a venue.
 *
 * This is the polymorphic side. A note does not know in advance what it will
 * be attached to, so it stores:
 *   - notable_id   : the id of the parent row
 *   - notable_type : the parent's class (App\Models\Event or App\Models\Venue)
 *
 * There is no foreign key for these columns because notable_id can point at
 * two different tables. So the DATABASE cannot stop a note that points at a
 * missing parent; only our code can.
 */
class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'body',
        'notable_id',
        'notable_type',
    ];

    /**
     * Polymorphic inverse: give me the parent model (an Event OR a Venue).
     * Laravel reads notable_type to decide which table to query.
     */
    public function notable(): MorphTo
    {
        return $this->morphTo();
    }
}
