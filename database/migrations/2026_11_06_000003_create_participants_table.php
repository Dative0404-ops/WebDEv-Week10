<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION 3 of 5: the "participants" table.
 *
 * A participant is a person who can join many events. This table is the
 * OTHER "many" side of the event <-> participant many-to-many link.
 * It must exist before the pivot table (event_participant) is created.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();                    // Primary key
            $table->string('name');          // Full name of the participant
            $table->string('email')->unique(); // Email is unique (a real-world unique rule)
            $table->timestamps();            // created_at / updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
