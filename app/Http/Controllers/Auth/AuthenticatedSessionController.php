<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Notifications\TwoFactorCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Two-factor is opt-in and only ever applies to SME owners who have
        // switched it on in their profile — admin accounts never use it.
        if ($user->isSmeOwner() && $user->two_factor_enabled) {
            $remember = $request->boolean('remember');

            // The password (and portal) has been verified, but the login is
            // deliberately left incomplete here: log back out and hold the
            // session in a "pending 2FA" state until the emailed code is
            // confirmed by TwoFactorController::store().
            Auth::logout();

            $code = (string) random_int(100000, 999999);
            $user->forceFill([
                'two_factor_code' => $code,
                'two_factor_expires_at' => now()->addMinutes(10),
            ])->save();

            $request->session()->put('two_factor_user_id', $user->id);
            $request->session()->put('two_factor_remember', $remember);

            try {
                $user->notify(new TwoFactorCode($code));
            } catch (Throwable $e) {
                // Mail server unreachable: keep the user on the code page so
                // they can use "Resend" once email is working again.
                report($e);

                return redirect()->route('two-factor.challenge')
                    ->withErrors(['code' => __('We could not send your code right now. Please wait a minute and choose "Resend".')]);
            }

            return redirect()->route('two-factor.challenge');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
