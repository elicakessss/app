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
        Schema::create('evaluation_peer_evaluators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade');
            $table->foreignId('evaluatee_student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('evaluator_student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('assigned_by_user_id')->constrained('users')->onDelete('cascade'); // Who assigned this peer evaluator (adviser)
            $table->text('assignment_notes')->nullable(); // Notes about why this student was assigned as peer evaluator
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();
            
            // Ensure unique peer evaluator assignments (one student can only be assigned once to evaluate another specific student in same evaluation)
            $table->unique(['evaluation_id', 'evaluatee_student_id', 'evaluator_student_id'], 'unique_peer_assignment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_peer_evaluators');
    }
};
