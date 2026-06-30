<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spaces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type')->default('OUTRO'); // CASA|EVENTO|VIAGEM|OUTRO
            $table->string('status')->default('aberto'); // aberto|fechado
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('color')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spaces');
    }
};
