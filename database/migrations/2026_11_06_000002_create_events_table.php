<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION 2 of 5: the "events" table.
 *
 * An event happens at exactly ONE venue. This is the "many" side of the
 * venue -> events one-to-many link, so the foreign key lives here.
 *
 * The foreign key uses ON DELETE RESTRICT. That is the database-level rule
 * that makes "a venue that still has events must not be deletable" REAL:
 * if any event row still points at a venue, PostgreSQL refuses the delete.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();                              // Primary key

            // Foreign key to venues. RESTRICT = PostgreSQL blocks deleting a
            // venue while at least one event still references it.
            $table->foreignId('venue_id')
                  ->constrained('venues')
                  ->restrictOnDelete();

            $table->string('title');                   // Event title
            $table->text('description')->nullable();   // Optional details
            $table->dateTime('starts_at')->nullable(); // When it starts
            $table->timestamps();                      // created_at / updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
