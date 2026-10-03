<x-guest-layout>
    <p class="text-center text-muted small text-uppercase mb-3" style="letter-spacing:.08em;">{{ __('Two-Factor Verification') }}</p>

    <p class="text-muted small text-center">{{ __('We emailed a 6-digit verification code to your registered email address. Enter it below to finish logging in.') }}</p>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('two-factor.store') }}">
        @csrf

        <div class="mb-3">
            <label for="code" class="form-label">{{ __('Verification Code') }}</label>
            <input id="code" type="text" name="code" class="form-control text-center fs-4" style="letter-spacing:.3em;" inputmode="numeric" autocomplete="one-time-code" maxlength="6" required autofocus>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-2">{{ __('Verify & Log In') }}</button>
    </form>

    <form method="POST" action="{{ route('two-factor.resend') }}" class="text-center">
        @csrf
        <button type="submit" class="btn btn-link btn-sm">{{ __("Didn't get a code? Resend it.") }}</button>
    </form>

    <p class="text-center small text-muted mb-0">
        <a href="{{ route('login') }}">{{ __('Cancel and return to login') }}</a>
    </p>
</x-guest-layout>
