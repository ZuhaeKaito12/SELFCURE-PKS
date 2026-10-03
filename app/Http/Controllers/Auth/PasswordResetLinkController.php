<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            Password::sendResetLink($request->only('email'));
        } catch (Throwable $e) {
            // The mail server could not be reached; tell the user instead of
            // showing an error page.
            report($e);

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => __('We could not send the email right now. Please try again in a few minutes.')]);
        }

        // The same message is shown whether or not the address is registered
        // (or was just sent a link), so this form cannot be used to find out
        // which emails have accounts.
        return back()->with('status', __('If an account exists for this email, we have sent a password reset link to it.'));
    }
}
