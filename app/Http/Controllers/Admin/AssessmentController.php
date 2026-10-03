<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Notifications\AdminNoteAdded;
use App\Services\ComplianceReportService;
use App\Services\MaturityScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    /**
     * Monitor assessment activity and completion status across every
     * registered SME client.
     */
    public function index(Request $request): View
    {
        $query = Assessment::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $assessments = $query->paginate(15)->withQueryString();

        return view('admin.assessments.index', ['assessments' => $assessments]);
    }

    /**
     * View a single client's assessment result and recommendations.
     */
    public function show(Assessment $assessment, ComplianceReportService $reportService, MaturityScoringService $scoring): View
    {
        $assessment->load(['domainScores.domain', 'user']);

        return view('admin.assessments.show', [
            'assessment' => $assessment,
            'recommendations' => $assessment->isCompleted()
                ? $reportService->prioritisedRecommendations($assessment)
                : collect(),
            'riskLevel' => $assessment->overall_score !== null
                ? $scoring->riskLevelForPercentage((float) $assessment->overall_score)
                : null,
            'scoring' => $scoring,
        ]);
    }

    /**
     * Save an administrator's manual audit notes/recommendations against a
     * client's completed assessment.
     */
    public function saveNotes(Request $request, Assessment $assessment): RedirectResponse
    {
        $validated = $request->validate([
            'auditor_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $assessment->update(['auditor_notes' => $validated['auditor_notes'] ?? null]);

        if (! empty($validated['auditor_notes'])) {
            $assessment->load('user');
            $assessment->user->notify(new AdminNoteAdded($assessment));
        }

        return back()->with('status', __('Auditor notes saved.'));
    }
}
