<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checkins', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('staff_link_id')->nullable()->constrained('checkin_staff_links')->nullOnDelete();
            $table->string('name');
            $table->string('group', 20)->nullable();
            $table->unsignedSmallInteger('pax_actual')->default(1);
            $table->string('method', 20);
            $table->text('device_info')->nullable();
            $table->timestamp('checked_in_at');
            $table->timestamps();

            $table->index(['invitation_id', 'checked_in_at']);
            $table->unique(['invitation_id', 'guest_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkins');
    }
};
