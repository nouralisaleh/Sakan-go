<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('city');
            $table->string('governorate');
            $table->unsignedInteger('rooms');
            $table->unsignedInteger('area');
            $table->decimal('price', 10, 2);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
    
};
