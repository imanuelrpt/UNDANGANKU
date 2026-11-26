<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rsvp_events', function (Blueprint $table): void {
            $table->foreignId('rsvp_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained('invitation_events')->cascadeOnDelete();
            $table->primary(['rsvp_id', 'event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rsvp_events');
    }
};
