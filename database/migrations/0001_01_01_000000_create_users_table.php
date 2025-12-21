<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->unique();
            $table->string('country_code');
            $table->timestamp('phone_verified_at')->nullable();
            $table->enum('status', ['newUser', 'pending', 'rejected', 'approved'])->default('newUser');
            $table->enum('role', ['tenant', 'owner'])->default('tenant');
            $table->text('rejected_reason')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
    
};
