<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('space_id')->constrained('spaces')->cascadeOnDelete();
            $table->foreignId('payer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('method')->nullable(); // pix|dinheiro|cartao|transferencia
            $table->string('status')->default('pendente'); // pendente|pago
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settlements');
    }
};
