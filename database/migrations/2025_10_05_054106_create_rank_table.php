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
        Schema::create('ranks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->decimal('final_score', 5, 3)->nullable(); // Final computed score
            $table->enum('rank', ['gold', 'silver', 'bronze', 'none'])->nullable();
            $table->enum('status', ['pending', 'finalized'])->default('pending');
            $table->json('breakdown')->nullable(); // Per evaluator contribution breakdown
            $table->timestamps();
            
            // Ensure only one final result per student per organization
            $table->unique(['organization_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ranks');
    }
};
