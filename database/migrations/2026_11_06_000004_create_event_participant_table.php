<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION 4 of 5: the "event_participant" table (the registration / pivot table).
 *
 * This table RESOLVES the many-to-many link between events and participants.
 * Because each registration also needs a "status", the pivot carries an extra
 * column ("status") and its own primary key.
 *
 * Two rules are enforced by PostgreSQL here:
 *   1. ON DELETE CASCADE  -> deleting an event or a participant automatically
 *      removes their registration rows ("their registrations must disappear too").
 *   2. UNIQUE(event_id, participant_id) -> the SAME participant cannot register
 *      for the SAME event twice.
 *
 * It must run AFTER both events and participants exist.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_participant', function (Blueprint $table) {
            $table->id(); // Own primary key (a pivot can have one)

            // Both foreign keys CASCADE on delete: remove the parent and the
            // matching registration rows vanish automatically.
            $table->foreignId('event_id')
                  ->constrained('events')
                  ->cascadeOnDelete();

            $table->foreignId('participant_id')
                  ->constrained('participants')
                  ->cascadeOnDelete();

            // Registration status. PostgreSQL stores this as a varchar with a
            // CHECK constraint that only allows these two words.
            $table->enum('status', ['registered', 'waitlisted'])->default('registered');

            $table->timestamps(); // created_at / updated_at for the registration

            // Composite UNIQUE: the database itself blocks a duplicate sign-up.
            $table->unique(['event_id', 'participant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_participant');
    }
};
