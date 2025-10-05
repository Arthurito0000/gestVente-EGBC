<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('password_reset_logs', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('ip_address');
            $table->text('user_agent')->nullable();
            $table->enum('action', ['request', 'reset', 'failed']);
            $table->string('status')->nullable(); // success, failed, expired, etc.
            $table->text('details')->nullable(); // JSON pour détails supplémentaires
            $table->timestamps();
            
            $table->index(['email', 'created_at']);
            $table->index(['ip_address', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_logs');
    }
};
