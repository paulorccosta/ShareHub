<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_id')->constrained('expenses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->decimal('fixed_value', 12, 2)->nullable();
            $table->decimal('share_amount', 12, 2)->nullable(); // computed final amount owed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_participants');
    }
};
