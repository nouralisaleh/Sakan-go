<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('password_reset_otps', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->timestamp('expires_at');
            $table->boolean('is_used')->default(false);
            $table->string('otp');
            $table->boolean('is_verified')->default(false);
            $table->integer('attempts')->default(0);
            $table->string('reset_token')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_otps');
    }

};
