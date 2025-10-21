<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Organization;
use App\Models\Evaluation;
use App\Models\Rank;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test students
        $student1 = Student::firstOrCreate([
            'school_number' => '2024-001001',
        ], [
            'name' => 'John Doe',
            'email' => 'john.doe@test.com',
            'password' => bcrypt('password'),
        ]);

        $student2 = Student::firstOrCreate([
            'school_number' => '2024-001002',
        ], [
            'name' => 'Jane Smith',
            'email' => 'jane.smith@test.com',
            'password' => bcrypt('password'),
        ]);

        // Get or create test organization
        $organization = Organization::first();
        if (!$organization) {
            $organization = Organization::create([
                'name' => 'Student Council',
                'year' => 2024,
                'description' => 'Main student government organization'
            ]);
        }

        // Create a sample evaluation event for this organization and attach students to it
        $evaluationEvent = Evaluation::firstOrCreate([
            'organization_id' => $organization->id,
            'user_id' => 1, // fallback admin user id
            'year' => 2024,
        ], [
            'name' => '2024 Organizational Evaluation',
        ]);

        // Attach students to the evaluation event with positions
        $evaluationEvent->students()->syncWithoutDetaching([
            $student1->id => ['position' => 'President'],
            $student2->id => ['position' => 'Vice President'],
        ]);

        // Create sample evaluation scores for students
        $this->createEvaluationsForStudent($evaluationEvent, $student1);
        $this->createEvaluationsForStudent($evaluationEvent, $student2);
    }

    private function createEvaluationsForStudent(Evaluation $evaluation, Student $student): void
    {
        // Adviser Evaluation (All questions)
        $adviserAnswers = [
            'domain_1_strand_1_q1' => 3.0,
            'domain_1_strand_1_q2' => 2.0,
            'domain_1_strand_1_q3' => 3.0,
            'domain_1_strand_1_q4' => 2.0,
            'domain_1_strand_2_q1' => 3.0,
            'domain_2_strand_1_q1' => 2.0,
            'domain_2_strand_2_q1' => 3.0,
            'domain_2_strand_2_q2' => 2.0,
            'domain_2_strand_3_q1' => 3.0,
            'domain_2_strand_3_q2' => 3.0,
            'domain_3_strand_1_q1' => 2.0,
            'domain_3_strand_2_q1' => 3.0,
            'length_of_service' => 2.0,
        ];

        // Adviser evaluation score
        \App\Models\EvaluationScore::updateOrCreate([
            'evaluation_id' => $evaluation->id,
            'student_id' => $student->id,
            'evaluator_type' => 'adviser',
            'evaluator_id' => null,
        ], [
            'answers' => $adviserAnswers,
        ]);

        // Peer Evaluation (Domain 2 all strands + Domain 3 strands 1-2)
        $peerAnswers = [
            'domain_2_strand_1_q1' => 2.0,
            'domain_2_strand_2_q1' => 3.0,
            'domain_2_strand_2_q2' => 2.0,
            'domain_2_strand_3_q1' => 3.0,
            'domain_2_strand_3_q2' => 2.0,
            'domain_3_strand_1_q1' => 3.0,
            'domain_3_strand_2_q1' => 2.0,
        ];

        // Peer evaluation score (use evaluator_id placeholder 2)
        \App\Models\EvaluationScore::updateOrCreate([
            'evaluation_id' => $evaluation->id,
            'student_id' => $student->id,
            'evaluator_type' => 'peer',
            'evaluator_id' => 2,
        ], [
            'answers' => $peerAnswers,
        ]);

        // Self Evaluation (Domain 2 strands 1-2 + Domain 3 all strands)
        $selfAnswers = [
            'domain_2_strand_1_q1' => 3.0,
            'domain_2_strand_2_q1' => 2.0,
            'domain_2_strand_2_q2' => 3.0,
            'domain_3_strand_1_q1' => 2.0,
            'domain_3_strand_2_q1' => 3.0,
        ];

        // Self evaluation score
        \App\Models\EvaluationScore::updateOrCreate([
            'evaluation_id' => $evaluation->id,
            'student_id' => $student->id,
            'evaluator_type' => 'self',
            'evaluator_id' => null,
        ], [
            'answers' => $selfAnswers,
        ]);
    }
}