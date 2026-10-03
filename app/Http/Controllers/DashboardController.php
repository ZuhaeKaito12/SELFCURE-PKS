<?php

namespace App\Http\Controllers;

use App\Services\ActionPlanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(ActionPlanService $actionPlan): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $latestAssessment = $user->assessments()
            ->where('status', 'completed')
            ->latest('completed_at')
            ->with('domainScores.domain')
            ->first();

        $inProgressAssessment = $user->assessments()
            ->where('status', 'in_progress')
            ->latest()
            ->first();

        $completedCount = $user->assessments()->where('status', 'completed')->count();

        $planItems = $actionPlan->itemsFor($user);
        $actionPlanTotal = $planItems->count();
        $actionPlanResolved = $planItems->where('status', 'completed')->count();

        $reassessmentDue = $latestAssessment
            && ! $inProgressAssessment
            && $latestAssessment->completed_at->lt(now()->subMonths(6));

        return view('dashboard', [
            'latestAssessment' => $latestAssessment,
            'inProgressAssessment' => $inProgressAssessment,
            'completedCount' => $completedCount,
            'actionPlanTotal' => $actionPlanTotal,
            'actionPlanResolved' => $actionPlanResolved,
            'actionPlanInProgress' => $planItems->where('status', 'in_progress')->count(),
            'nextActions' => $planItems->reject->isCompleted()->take(3)->values(),
            'reassessmentDue' => $reassessmentDue,
            'hasSeenTour' => $user->has_seen_tour,
        ]);
    }
}
