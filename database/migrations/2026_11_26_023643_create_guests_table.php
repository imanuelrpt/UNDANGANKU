<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('invitation_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('group', 20)->default('teman');
            $table->string('phone', 30)->nullable();
            $table->unsignedSmallInteger('pax_quota')->default(1);
            $table->string('token', 64)->unique();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->unsignedInteger('open_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['invitation_id', 'group']);
            $table->index(['invitation_id', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
