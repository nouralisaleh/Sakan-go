<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->foreignId('apartment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('tenant_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('owner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('firebase_chat_id')->unique();
            $table->timestamps();

            $table->unique(['apartment_id', 'tenant_id','owner_id']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
