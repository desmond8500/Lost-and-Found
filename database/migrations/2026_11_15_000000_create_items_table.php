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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20);
            $table->string('category', 40);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('name_on_item')->nullable();
            $table->string('city');
            $table->string('location')->nullable();
            $table->date('occurred_on');
            $table->string('photo_path')->nullable();
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('manage_token_hash', 64);
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'category']);
            $table->index('city');
            $table->index('resolved_at');
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->string('sender_name');
            $table->string('sender_email');
            $table->text('body');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('items');
    }
};
