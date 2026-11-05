<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION 5 of 5: the "notes" table (the POLYMORPHIC link).
 *
 * A note can belong to EITHER an event OR a venue, using ONE table.
 * It does this with two columns:
 *   - notable_id   : the id of the parent row
 *   - notable_type : the class of the parent ("App\Models\Event" or "App\Models\Venue")
 *
 * IMPORTANT: there is NO foreign key on these columns, and that is on purpose.
 * notable_id can point at more than one table, so PostgreSQL has no single
 * table it could enforce the reference against. Keeping orphan notes out is a
 * job our code has to do, not the database. (See Step 8 of the exercise.)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();          // Primary key
            $table->text('body');  // The note text

            // morphs() creates notable_id (bigint) + notable_type (string)
            // plus an index on the pair. No foreign key is created.
            $table->morphs('notable');

            $table->timestamps();  // created_at / updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
