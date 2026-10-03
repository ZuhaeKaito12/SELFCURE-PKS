<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActionPlanService;
use App\Models\User;
use App\Notifications\AdminMessage;
use App\Services\ComplianceReportService;
use App\Services\MaturityScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('admin.dashboard');
    }

    public function create(): View
    {
        return view('admin.clients.create', [
            'businessTypes' => User::BUSINESS_TYPES,
            'states' => User::STATES,
        ]);
    }

    /**
     * Register a new SME client account directly from the admin portal.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'business_name' => ['required', 'string', 'max:255'],
            'ssm_number' => ['nullable', 'string', 'max:255'],
            'business_type' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $client = User::create([
            ...$validated,
            'password' => Hash::make($validated['password']),
            'role' => 'sme_owner',
        ]);

        return redirect()->route('admin.clients.show', $client)
            ->with('status', __('SME client account created.'));
    }

    /**
     * Show a client's tabbed audit profile: detailed audit breakdown,
     * company profile, and assessment history.
     */
    public function show(User $client, ComplianceReportService $reportService, MaturityScoringService $scoring, ActionPlanService $actionPlan): View
    {
        abort_unless($client->role === 'sme_owner', 404);

        $client->load(['assessments' => function ($query) {
            $query->latest();
        }, 'assessments.domainScores.domain']);

        $latestAssessment = $client->assessments->firstWhere('status', 'completed');

        $breakdown = collect();
        if ($latestAssessment) {
            $breakdown = $latestAssessment->answers()
                ->with('question.domain')
                ->get()
                ->sortBy([
                    fn ($a, $b) => $a->question->domain->order <=> $b->question->domain->order,
                    fn ($a, $b) => $a->question->order <=> $b->question->order,
                ])
                ->map(fn ($answer) => [
                    'domain' => $answer->question->domain->translatedName(),
                    'text' => $answer->question->translatedText(),
                    'score' => $answer->score,
                    'status' => $scoring->questionStatusForScore($answer->score),
                ]);
        }

        $actionPlanTasks = $actionPlan->itemsFor($client);

        return view('admin.clients.show', [
            'client' => $client,
            'latestAssessment' => $latestAssessment,
            'breakdown' => $breakdown,
            'recommendations' => $latestAssessment
                ? $reportService->prioritisedRecommendations($latestAssessment)
                : collect(),
            'actionPlanTasks' => $actionPlanTasks,
            'actionPlanResolved' => $actionPlanTasks->where('status', 'completed')->count(),
        ]);
    }

    /**
     * Send a one-off notification from the admin to this SME owner, e.g.
     * to flag something the auditor notes field doesn't cover well.
     */
    public function notify(Request $request, User $client): RedirectResponse
    {
        abort_unless($client->role === 'sme_owner', 404);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $client->notify(new AdminMessage($validated['message']));

        return back()->with('status', __('Notification sent to :name.', ['name' => $client->business_name ?? $client->name]));
    }

    public function edit(User $client): View
    {
        abort_unless($client->role === 'sme_owner', 404);

        return view('admin.clients.edit', [
            'client' => $client,
            'businessTypes' => User::BUSINESS_TYPES,
            'states' => User::STATES,
        ]);
    }

    public function update(Request $request, User $client): RedirectResponse
    {
        abort_unless($client->role === 'sme_owner', 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'business_name' => ['required', 'string', 'max:255'],
            'ssm_number' => ['nullable', 'string', 'max:255'],
            'business_type' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($client->id)],
        ]);

        $client->update($validated);

        return redirect()->route('admin.clients.show', $client)
            ->with('status', __('Client account updated.'));
    }

    public function destroy(User $client): RedirectResponse
    {
        abort_unless($client->role === 'sme_owner', 404);

        $client->delete();

        return redirect()->route('admin.dashboard')
            ->with('status', __('Client account removed.'));
    }
}
