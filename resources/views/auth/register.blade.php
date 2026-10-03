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

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Your Name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" class="form-control" required autofocus autocomplete="name">
        </div>

        <div class="mb-3">
            <label for="business_name" class="form-label">{{ __('Business Name') }}</label>
            <input id="business_name" type="text" name="business_name" value="{{ old('business_name') }}" class="form-control" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="ssm_number" class="form-label">{{ __('SSM Registration Number') }} <span class="text-muted">({{ __('optional') }})</span></label>
                <input id="ssm_number" type="text" name="ssm_number" value="{{ old('ssm_number') }}" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">{{ __('Phone Number') }} <span class="text-muted">({{ __('optional') }})</span></label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}" class="form-control">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="business_type" class="form-label">{{ __('Industry') }} <span class="text-muted">({{ __('optional') }})</span></label>
                <select id="business_type" name="business_type" class="form-select">
                    <option value="">{{ __('Select industry') }}</option>
                    @foreach ($businessTypes as $type)
                        <option value="{{ $type }}" {{ old('business_type') === $type ? 'selected' : '' }}>{{ __($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="state" class="form-label">{{ __('State') }} <span class="text-muted">({{ __('optional') }})</span></label>
                <select id="state" name="state" class="form-select">
                    <option value="">{{ __('Select state') }}</option>
                    @foreach ($states as $state)
                        <option value="{{ $state }}" {{ old('state') === $state ? 'selected' : '' }}>{{ $state }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required autocomplete="username">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input id="password" type="password" name="password" class="form-control" required autocomplete="new-password">
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
        </div>

        <div class="d-flex align-items-center justify-content-between">
            <a class="small text-muted" href="{{ route('login') }}">{{ __('Already registered?') }}</a>
            <button type="submit" class="btn btn-primary">{{ __('Register') }}</button>
        </div>
    </form>
</x-guest-layout>
