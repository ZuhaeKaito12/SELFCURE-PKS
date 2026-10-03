<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold mb-0">{{ __('Register New SME') }}</h2>
    </x-slot>

    <div class="card shadow-sm col-lg-7">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.clients.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('Owner Name') }}</label>
                    <input id="name" name="name" type="text" class="form-control" value="{{ old('name') }}" required autofocus>
                    @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="business_name" class="form-label">{{ __('Business Name') }}</label>
                    <input id="business_name" name="business_name" type="text" class="form-control" value="{{ old('business_name') }}" required>
                    @error('business_name') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="ssm_number" class="form-label">{{ __('SSM Number') }}</label>
                        <input id="ssm_number" name="ssm_number" type="text" class="form-control" value="{{ old('ssm_number') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">{{ __('Phone') }}</label>
                        <input id="phone" name="phone" type="text" class="form-control" value="{{ old('phone') }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="business_type" class="form-label">{{ __('Industry') }}</label>
                        <select id="business_type" name="business_type" class="form-select">
                            <option value="">{{ __('Select industry') }}</option>
                            @foreach ($businessTypes as $type)
                                <option value="{{ $type }}" {{ old('business_type') === $type ? 'selected' : '' }}>{{ __($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="state" class="form-label">{{ __('State') }}</label>
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
                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">{{ __('Temporary Password') }}</label>
                        <input id="password" name="password" type="password" class="form-control" required>
                        @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">{{ __('Create Client') }}</button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
