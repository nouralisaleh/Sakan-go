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
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('city');
            $table->string('governorate');
            $table->unsignedInteger('rooms');
            $table->string('area');
            $table->unsignedInteger('price');
            $table->boolean('is_furnished')->default(false);
            $table->unsignedInteger('floor_number');
            $table->unsignedInteger('size');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }

};
