<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('otp_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('token')->unique();
            $table->string('phone_number');
            $table->string('country_code');
            $table->timestamp('expires_at');
            $table->boolean('used')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_sessions');
    }
};
