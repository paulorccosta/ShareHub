<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('space_id')->constrained('spaces')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // who registered/paid
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->date('expense_date');
            $table->string('receipt')->nullable(); // storage path
            $table->text('notes')->nullable();
            $table->string('status')->default('confirmado'); // pendente|confirmado
            $table->string('split_type')->default('igual'); // igual|personalizada|percentual|valor_fixo
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
