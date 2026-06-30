<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('space_id')->constrained('spaces')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role')->default('participante'); // admin|editor|participante|visualizador
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['space_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
