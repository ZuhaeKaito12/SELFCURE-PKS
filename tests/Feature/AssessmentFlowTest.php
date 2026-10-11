<?php

namespace Tests\Feature;

use App\Models\ActionPlanItem;
use App\Models\Assessment;
use App\Models\Domain;
use App\Models\Question;
use App\Models\User;
use Database\Seeders\DomainSeeder;
use Database\Seeders\QuestionSeeder;
use Database\Seeders\SecurityActionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssessmentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Use the rule-based assistant so the tests never call the Gemini API.
        config(['ai.gemini_api_key' => null]);

        $this->seed([DomainSeeder::class, QuestionSeeder::class, SecurityActionSeeder::class]);
    }

    private function owner(): User
    {
        return User::factory()->create(['role' => 'sme_owner', 'business_name' => 'Test Enterprise']);
    }

    public function test_starting_an_assessment_resumes_the_one_in_progress(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)->get('/assessment/start')->assertRedirect();
        $this->actingAs($owner)->get('/assessment/start')->assertRedirect();

        $this->assertSame(1, $owner->assessments()->count());
    }

    public function test_answers_are_autosaved_one_at_a_time(): void
    {
        $owner = $this->owner();
        $assessment = $owner->assessments()->create(['status' => 'in_progress']);
        $question = Question::first();

        $this->actingAs($owner)
            ->postJson("/assessment/{$assessment->id}/answer", ['question_id' => $question->id, 'score' => 4])
            ->assertOk()
            ->assertJson(['saved' => true]);

        $this->assertDatabaseHas('assessment_answers', [
            'assessment_id' => $assessment->id, 'question_id' => $question->id, 'score' => 4,
        ]);
    }

    public function test_an_answer_outside_the_1_to_5_scale_is_rejected(): void
    {
        $owner = $this->owner();
        $assessment = $owner->assessments()->create(['status' => 'in_progress']);

        $this->actingAs($owner)
            ->postJson("/assessment/{$assessment->id}/answer", ['question_id' => Question::first()->id, 'score' => 6])
            ->assertStatus(422);

        $this->assertDatabaseCount('assessment_answers', 0);
    }

    public function test_submission_stores_scores_matching_the_manual_calculation(): void
    {
        $owner = $this->owner();
        $assessment = $owner->assessments()->create(['status' => 'in_progress']);

        // Answer every question in a domain with the same score: Identify 1,
        // Protect 2, Detect 3, Respond 4, Recover 5.
        $scoreByDomain = ['identify' => 1, 'protect' => 2, 'detect' => 3, 'respond' => 4, 'recover' => 5];
        $answers = [];
        foreach (Domain::with('questions')->get() as $domain) {
            foreach ($domain->questions as $question) {
                $answers[$question->id] = $scoreByDomain[$domain->code];
            }
        }

        $this->actingAs($owner)
            ->post("/assessment/{$assessment->id}/questionnaire", ['answers' => $answers])
            ->assertRedirect("/assessment/{$assessment->id}/result");

        $assessment->refresh();
        $this->assertSame('completed', $assessment->status);
        // Domain percentages 0, 25, 50, 75 and 100 give an overall score of 50.
        $this->assertEquals(50.00, (float) $assessment->overall_score);
        $this->assertSame('Developing', $assessment->overall_level);

        $protect = Domain::where('code', 'protect')->first();
        $this->assertDatabaseHas('assessment_domain_scores', [
            'assessment_id' => $assessment->id, 'domain_id' => $protect->id, 'level' => 'Initial',
        ]);
    }

    public function test_answers_of_1_or_2_become_action_plan_items(): void
    {
        $owner = $this->owner();
        $assessment = $owner->assessments()->create(['status' => 'in_progress']);
        $questions = Question::orderBy('id')->get();

        $answers = $questions->mapWithKeys(fn ($q) => [$q->id => 5])->all();
        $answers[$questions[0]->id] = 1;
        $answers[$questions[1]->id] = 2;

        $this->actingAs($owner)->post("/assessment/{$assessment->id}/questionnaire", ['answers' => $answers]);

        $items = ActionPlanItem::where('user_id', $owner->id)->get();
        $this->assertCount(2, $items);
        $this->assertEqualsCanonicalizing([1, 2], $items->pluck('answer_score')->all());
        $this->assertTrue($items->every(fn ($item) => $item->status === 'not_started'));
    }

    public function test_ticking_every_step_completes_the_action(): void
    {
        $owner = $this->owner();
        $assessment = $owner->assessments()->create(['status' => 'in_progress']);
        $answers = Question::all()->mapWithKeys(fn ($q) => [$q->id => 5])->all();
        $answers[Question::first()->id] = 1;
        $this->actingAs($owner)->post("/assessment/{$assessment->id}/questionnaire", ['answers' => $answers]);

        $item = ActionPlanItem::where('user_id', $owner->id)->firstOrFail();

        $this->actingAs($owner)->patch("/action-plan/{$item->id}/steps", ['step' => 0, 'done' => true]);
        $this->assertSame('in_progress', $item->fresh()->status);

        foreach (range(1, $item->stepCount() - 1) as $step) {
            $this->actingAs($owner)->patch("/action-plan/{$item->id}/steps", ['step' => $step, 'done' => true]);
        }
        $this->assertSame('completed', $item->fresh()->status);
    }

    public function test_result_page_and_pdf_report_are_available_after_submission(): void
    {
        $owner = $this->owner();
        $assessment = $owner->assessments()->create(['status' => 'in_progress']);
        $answers = Question::all()->mapWithKeys(fn ($q) => [$q->id => 3])->all();
        $this->actingAs($owner)->post("/assessment/{$assessment->id}/questionnaire", ['answers' => $answers]);

        $this->actingAs($owner)->get("/assessment/{$assessment->id}/result")->assertOk()->assertSee('50');

        $this->actingAs($owner)->get("/assessment/{$assessment->id}/report")
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
