<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('phone_otps', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->string('country_code')->default('+963');
            $table->string('otp');
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_used')->default(false);
            $table->dateTime('expires_at');
            $table->boolean('used_for_profile')->default(false);
            $table->timestamp('resend_available_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phone_otps');
    }

};
