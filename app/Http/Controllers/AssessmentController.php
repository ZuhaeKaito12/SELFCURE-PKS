<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentAnswer;
use App\Models\Domain;
use App\Services\ActionPlanService;
use App\Services\AiRecommendationService;
use App\Services\ComplianceReportService;
use App\Services\MaturityScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    /**
     * Start a new assessment, or resume one already in progress for the
     * logged-in SME owner, then send them to the questionnaire.
     */
    public function start(): RedirectResponse
    {
        $user = Auth::user();

        $assessment = $user->assessments()
            ->where('status', 'in_progress')
            ->latest()
            ->first();

        if (! $assessment) {
            $assessment = $user->assessments()->create([
                'status' => 'in_progress',
            ]);
        }

        return redirect()->route('assessment.questionnaire', $assessment);
    }

    /**
     * Show the self-assessment questionnaire covering all five NIST CSF
     * domains, pre-filled with any answers already saved for this attempt.
     */
    public function questionnaire(Assessment $assessment): View
    {
        $this->authoriseOwner($assessment);

        if ($assessment->isCompleted()) {
            return $this->questionnaireForReview($assessment);
        }

        $domains = Domain::with('questions')->orderBy('order')->get();
        $existingAnswers = $assessment->answers()->pluck('score', 'question_id');

        return view('assessment.questionnaire', [
            'assessment' => $assessment,
            'domains' => $domains,
            'existingAnswers' => $existingAnswers,
            'scaleLabels' => MaturityScoringService::scaleLabels(),
        ]);
    }

    private function questionnaireForReview(Assessment $assessment): View
    {
        $domains = Domain::with('questions')->orderBy('order')->get();
        $existingAnswers = $assessment->answers()->pluck('score', 'question_id');

        return view('assessment.questionnaire', [
            'assessment' => $assessment,
            'domains' => $domains,
            'existingAnswers' => $existingAnswers,
            'scaleLabels' => MaturityScoringService::scaleLabels(),
            'readOnly' => true,
        ]);
    }

    /**
     * Save the submitted answers, calculate the domain and overall
     * maturity scores, and mark the assessment as completed.
     */
    public function submit(Request $request, Assessment $assessment, MaturityScoringService $scoring, ActionPlanService $actionPlan, AiRecommendationService $aiRecommendations): RedirectResponse
    {
        $this->authoriseOwner($assessment);

        if ($assessment->isCompleted()) {
            return redirect()->route('assessment.result', $assessment);
        }

        $questionIds = Domain::with('questions:id,domain_id')->get()
            ->pluck('questions')
            ->flatten()
            ->pluck('id');

        $rules = [];
        foreach ($questionIds as $id) {
            $rules["answers.$id"] = ['required', 'integer', 'between:1,5'];
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($validated, $assessment) {
            foreach ($validated['answers'] as $questionId => $score) {
                AssessmentAnswer::updateOrCreate(
                    ['assessment_id' => $assessment->id, 'question_id' => $questionId],
                    ['score' => $score]
                );
            }

            $assessment->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        });

        $assessment = $scoring->calculateAndStore($assessment);
        $actionPlan->syncFromAssessment($assessment);

        $aiRecommendations->insightsFor($assessment->load(['domainScores.domain', 'user']));

        return redirect()->route('assessment.result', $assessment)
            ->with('status', __('Assessment completed. Here is your Cyber Maturity Score.'));
    }

    /**
     * Autosave a single answer as the SME owner progresses through the
     * questionnaire, so the assessment can be safely resumed later.
     */
    public function saveAnswer(Request $request, Assessment $assessment): JsonResponse
    {
        $this->authoriseOwner($assessment);

        if ($assessment->isCompleted()) {
            return response()->json(['saved' => false], 409);
        }

        $validated = $request->validate([
            'question_id' => ['required', 'integer', 'exists:questions,id'],
            'score' => ['required', 'integer', 'between:1,5'],
        ]);

        AssessmentAnswer::updateOrCreate(
            ['assessment_id' => $assessment->id, 'question_id' => $validated['question_id']],
            ['score' => $validated['score']]
        );

        return response()->json(['saved' => true]);
    }

    /**
     * Show the maturity score result and prioritised recommendations.
     */
    public function result(Assessment $assessment, ComplianceReportService $reportService, MaturityScoringService $scoring, AiRecommendationService $aiRecommendations): View|RedirectResponse
    {
        $this->authoriseView($assessment);

        // An unfinished assessment has no score yet (e.g. an old bookmark, or Back
        // after starting a retake): send the owner to finish it instead of failing.
        if (! $assessment->isCompleted()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.assessments.show', $assessment)
                : redirect()->route('assessment.questionnaire', $assessment)
                    ->with('status', __('This assessment is not finished yet. Answer the remaining questions to see your result.'));
        }

        $assessment->load(['domainScores.domain', 'user']);
        $recommendations = $reportService->prioritisedRecommendations($assessment);

        return view('assessment.result', [
            'assessment' => $assessment,
            'recommendations' => $recommendations,
            'insights' => $aiRecommendations->insightsFor($assessment),
            'riskLevel' => $scoring->riskLevelForPercentage((float) $assessment->overall_score),
            'scoring' => $scoring,
        ]);
    }

    /**
     * "Explain my results": a longer plain-language walkthrough from the
     * assistant, generated on demand.
     */
    public function explain(Assessment $assessment, AiRecommendationService $aiRecommendations): JsonResponse
    {
        $this->authoriseView($assessment);
        abort_unless($assessment->isCompleted(), 404);

        $assessment->load(['domainScores.domain', 'user']);

        return response()->json($aiRecommendations->explain($assessment));
    }

    /**
     * List the logged-in SME owner's past assessments, most recent first.
     */
    public function history(): View
    {
        $assessments = Auth::user()->assessments()
            ->where('status', 'completed')
            ->latest('completed_at')
            ->get();

        return view('assessment.history', [
            'assessments' => $assessments,
        ]);
    }

    private function authoriseOwner(Assessment $assessment): void
    {
        abort_unless($assessment->user_id === Auth::id(), 403);
    }

    private function authoriseView(Assessment $assessment): void
    {
        abort_unless(
            $assessment->user_id === Auth::id() || Auth::user()->isAdmin(),
            403
        );
    }
}
