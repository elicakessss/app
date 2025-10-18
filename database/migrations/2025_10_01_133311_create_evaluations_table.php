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
            $table->foreignId('evaluator_id')->nullable()->constrained('students')->onDelete('cascade'); // For peer evaluations - which student did the evaluation
            $table->json('answers'); // Store all question answers
            $table->decimal('evaluator_score', 5, 3); // The computed score for this evaluator
            $table->timestamps();
            
            // For self evaluations: evaluator_id should be NULL
            // For peer evaluations: evaluator_id should be the student who did the evaluation
            // For adviser evaluations: evaluator_id should be NULL (done by admin user)
            
            // Ensure only one evaluation per student per evaluator type per organization (and per evaluator for peer evaluations)
            $table->unique(['organization_id', 'student_id', 'evaluator_type', 'evaluator_id'], 'unique_evaluation');
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
