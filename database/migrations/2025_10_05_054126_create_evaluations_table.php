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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->enum('evaluator_type', ['adviser', 'peer', 'self', 'length_of_service']);
            $table->json('answers'); // Store all question answers
            $table->decimal('evaluator_score', 5, 3); // The computed score for this evaluator
            $table->timestamps();
            
            // Ensure only one evaluation per student per evaluator type per organization
            $table->unique(['organization_id', 'student_id', 'evaluator_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
