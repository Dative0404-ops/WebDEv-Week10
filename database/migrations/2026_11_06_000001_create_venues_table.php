<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * MIGRATION 1 of 5: the "venues" table.
 *
 * A venue is the place where events happen. It is the "one" side of the
 * one-to-many link "a venue hosts many events".
 * This migration must run BEFORE events because events.venue_id points here.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();                              // Primary key (bigint, auto increment)
            $table->string('name');                    // Venue name, e.g. "Grand Auditorium"
            $table->string('address')->nullable();     // Optional street / campus location
            $table->unsignedInteger('capacity')->default(0); // How many people fit
            $table->timestamps();                      // created_at / updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
