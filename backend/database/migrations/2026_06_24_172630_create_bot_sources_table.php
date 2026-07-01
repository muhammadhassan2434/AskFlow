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
        Schema::create('bot_sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bot_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type', [
                'document',
                'website',
                'text',
            ]);

            $table->string('title');

            $table->longText('content')->nullable();

            $table->string('url')->nullable();

            $table->string('file_name')->nullable();

            $table->string('file_path')->nullable();

            $table->string('file_type')->nullable();

            $table->unsignedBigInteger('file_size')->nullable();

            $table->string('status')->default('pending');

            $table->text('error_message')->nullable();

            $table->json('meta')->nullable();

            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bot_sources');
    }
};
