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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('workspace_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('uploaded_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title');
            $table->string('file_name');
            $table->string('file_path');

            $table->string('file_type')->nullable(); // pdf, docx, txt
            $table->bigInteger('file_size')->nullable();

            $table->text('description')->nullable();

            // AI processing status
            $table->enum('status', [
                'uploaded',
                'processing',
                'processed',
                'failed'
            ])->default('uploaded');

            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
