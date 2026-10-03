<?php

namespace App\Http\Controllers;

use App\Models\ActionPlanItem;
use App\Notifications\ActionPlanCompleted;
use App\Services\ActionPlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ActionPlanController extends Controller
{
    public function __construct(private ActionPlanService $actionPlan)
    {
    }

    /**
     * The SME's roadmap: concrete actions generated from assessment gaps,
     * open ones first by priority, each with steps and a task status.
     */
    public function index(): View
    {
        $user = Auth::user();
        $items = $this->actionPlan->itemsFor($user);

        return view('action-plan.index', [
            'openItems' => $items->reject->isCompleted()->values(),
            'completedItems' => $items->filter->isCompleted()->values(),
            'summary' => $this->actionPlan->summary($items),
            'hasCompletedAssessment' => $user->assessments()->where('status', 'completed')->exists(),
        ]);
    }

    public function update(Request $request, ActionPlanItem $item): JsonResponse|RedirectResponse
    {
        // Ownership first, so another user's item is always a plain 403.
        abort_unless($item->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'status' => ['required', Rule::in(ActionPlanItem::STATUSES)],
        ]);

        return $this->apply($request, $item, fn () => $item->setStatus($validated['status']));
    }

    public function toggleStep(Request $request, ActionPlanItem $item): JsonResponse|RedirectResponse
    {
        abort_unless($item->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'step' => ['required', 'integer', 'min:0', 'max:'.max($item->stepCount() - 1, 0)],
            'done' => ['required', 'boolean'],
        ]);

        return $this->apply($request, $item, fn () => $item->toggleStep((int) $validated['step'], (bool) $validated['done']));
    }

    private function apply(Request $request, ActionPlanItem $item, callable $change): JsonResponse|RedirectResponse
    {
        abort_unless($item->user_id === Auth::id(), 403);

        $item->load('action');
        $wasAllDone = $this->allCompleted();

        $change();
        $item->save();

        $allDone = $this->allCompleted();
        $justFinished = $allDone && ! $wasAllDone;

        if ($justFinished) {
            Auth::user()->notify(new ActionPlanCompleted());
        }

        if ($request->expectsJson()) {
            $summary = $this->actionPlan->summary(ActionPlanItem::where('user_id', Auth::id())->get());

            return response()->json([
                'status' => $item->status,
                'done_steps' => $item->doneSteps(),
                'progress' => $item->progressPercent(),
                'summary' => $summary,
                'plan_complete' => $justFinished,
            ]);
        }

        return back()
            ->with('status', __('Action Roadmap updated.'))
            ->with('achievement_unlocked', $justFinished);
    }

    private function allCompleted(): bool
    {
        return ActionPlanItem::where('user_id', Auth::id())->exists()
            && ActionPlanItem::where('user_id', Auth::id())->where('status', '!=', 'completed')->doesntExist();
    }
}
