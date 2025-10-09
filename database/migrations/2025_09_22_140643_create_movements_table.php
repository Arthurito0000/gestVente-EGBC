<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['ENTREE', 'SORTIE', 'AJUSTEMENT']); // 🔴 IMPORTANT : avec guillemets
            $table->enum('ajustement_type', ['AUGMENTATIF', 'DIMINUTIF'])->nullable();
            $table->decimal('quantite', 10, 3);
            $table->decimal('prix_achat', 10, 2)->nullable();
            $table->string('motif');
            $table->timestamp('date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};