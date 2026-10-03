<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\ActionPlanItem;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $actionPlanComplete = false;

        if ($user->isSmeOwner()) {
            $hasCompletedAssessment = $user->assessments()->where('status', 'completed')->exists();
            $items = ActionPlanItem::where('user_id', $user->id)->get();
            $actionPlanComplete = $hasCompletedAssessment
                && ($items->isEmpty() || $items->every->isCompleted());
        }

        return view('profile.edit', [
            'user' => $user,
            'businessTypes' => User::BUSINESS_TYPES,
            'states' => User::STATES,
            'actionPlanComplete' => $actionPlanComplete,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Turn two-factor authentication on or off for the current SME owner.
     * Admin accounts never use two-factor, so this is intentionally
     * unreachable for them (no toggle is rendered on their profile page).
     */
    public function updateTwoFactor(Request $request): RedirectResponse
    {
        abort_unless($request->user()->isSmeOwner(), 403);

        $validated = $request->validate([
            'two_factor_enabled' => ['required', 'boolean'],
        ]);

        $request->user()->update(['two_factor_enabled' => $validated['two_factor_enabled']]);

        return Redirect::route('profile.edit')->with('status', $validated['two_factor_enabled']
            ? __('Two-factor authentication enabled.')
            : __('Two-factor authentication disabled.'));
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
