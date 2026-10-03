<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\TwoFactorCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class TwoFactorController extends Controller
{
    /**
     * Show the "enter the code we emailed you" form. Only reachable
     * mid-login, right after a correct password was supplied.
     */
    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('two_factor_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Verify the emailed code and, if correct, complete the login that
     * LoginRequest::authenticate() started but deliberately did not finish.
     */
    public function store(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('two_factor_user_id');
        abort_unless($userId, 403);

        $user = User::findOrFail($userId);

        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $codeIsValid = $user->two_factor_code !== null
            && hash_equals($user->two_factor_code, $request->string('code')->value())
            && $user->two_factor_expires_at !== null
            && ! $user->two_factor_expires_at->isPast();

        if (! $codeIsValid) {
            throw ValidationException::withMessages([
                'code' => __('That code is invalid or has expired.'),
            ]);
        }

        $user->forceFill([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ])->save();

        $remember = $request->session()->pull('two_factor_remember', false);
        $request->session()->forget('two_factor_user_id');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Issue a fresh code, in case the first one expired or never arrived.
     */
    public function resend(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('two_factor_user_id');
        abort_unless($userId, 403);

        $user = User::findOrFail($userId);

        $code = (string) random_int(100000, 999999);

        $user->forceFill([
            'two_factor_code' => $code,
            'two_factor_expires_at' => now()->addMinutes(10),
        ])->save();

        try {
            $user->notify(new TwoFactorCode($code));
        } catch (Throwable $e) {
            report($e);

            return back()->withErrors(['code' => __('We could not send your code right now. Please wait a minute and choose "Resend".')]);
        }

        return back()->with('status', __('A new code has been sent to your email.'));
    }
}
