<?php

use Database\Seeders\SecurityActionSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('title_ms')->nullable();
            $table->string('topic');
            $table->string('topic_ms')->nullable();
            $table->text('finding');
            $table->text('finding_ms')->nullable();
            $table->text('description');
            $table->text('description_ms')->nullable();
            $table->json('steps');
            $table->json('steps_ms')->nullable();
            $table->timestamps();
        });

        (new SecurityActionSeeder())->run();

        Schema::create('action_plan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('security_action_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessment_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('answer_score')->nullable();
            $table->string('status', 20)->default('not_started');
            $table->json('completed_steps')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'security_action_id']);
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->json('ai_insights')->nullable()->after('ai_summary');
        });

        $this->copyLegacyTasks();

        Schema::dropIfExists('remediation_tasks');
    }

    public function down(): void
    {
        Schema::create('remediation_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['not_started', 'in_progress', 'resolved'])->default('not_started');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'question_id']);
        });

        $items = DB::table('action_plan_items')
            ->join('security_actions', 'security_actions.id', '=', 'action_plan_items.security_action_id')
            ->select('action_plan_items.*', 'security_actions.question_id')
            ->get();

        foreach ($items as $item) {
            DB::table('remediation_tasks')->insert([
                'user_id' => $item->user_id,
                'question_id' => $item->question_id,
                'status' => $item->status === 'completed' ? 'resolved' : $item->status,
                'resolved_at' => $item->completed_at,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ]);
        }

        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn('ai_insights');
        });
        Schema::dropIfExists('action_plan_items');
        Schema::dropIfExists('security_actions');
    }

    private function copyLegacyTasks(): void
    {
        if (! Schema::hasTable('remediation_tasks')) {
            return;
        }

        $actions = DB::table('security_actions')->get()->keyBy('question_id');

        foreach (DB::table('remediation_tasks')->get() as $task) {
            $action = $actions->get($task->question_id);

            if (! $action) {
                continue;
            }

            $latestAnswer = DB::table('assessment_answers')
                ->join('assessments', 'assessments.id', '=', 'assessment_answers.assessment_id')
                ->where('assessments.user_id', $task->user_id)
                ->where('assessments.status', 'completed')
                ->where('assessment_answers.question_id', $task->question_id)
                ->orderByDesc('assessments.completed_at')
                ->select('assessment_answers.score', 'assessments.id as assessment_id')
                ->first();

            $completed = $task->status === 'resolved';
            $stepCount = count(json_decode($action->steps, true) ?? []);

            DB::table('action_plan_items')->insert([
                'user_id' => $task->user_id,
                'security_action_id' => $action->id,
                'assessment_id' => $latestAnswer?->assessment_id,
                'answer_score' => $latestAnswer?->score,
                'status' => $completed ? 'completed' : $task->status,
                'completed_steps' => json_encode($completed ? range(0, $stepCount - 1) : []),
                'completed_at' => $completed ? ($task->resolved_at ?? $task->updated_at) : null,
                'created_at' => $task->created_at,
                'updated_at' => $task->updated_at,
            ]);
        }
    }
};
