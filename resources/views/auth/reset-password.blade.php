<x-guest-layout>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" class="form-control" required autofocus autocomplete="username">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('New Password') }}</label>
            <input id="password" type="password" name="password" class="form-control" required autocomplete="new-password">
            <div class="form-text">{{ __('At least 8 characters, with uppercase and lowercase letters and a number.') }}</div>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">{{ __('Reset Password') }}</button>
        </div>
    </form>
</x-guest-layout>
