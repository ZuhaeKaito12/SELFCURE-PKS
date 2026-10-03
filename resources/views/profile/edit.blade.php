<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold mb-0">{{ __('Profile') }}</h2>
    </x-slot>

    @if ($user->isSmeOwner())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">{{ __('Achievements') }}</h5>
                @if ($actionPlanComplete)
                    <div class="achievement-badge">
                        <div class="achievement-badge-icon">✓</div>
                        <div>
                            <div class="achievement-badge-title">{{ __('Action Plan Complete') }}</div>
                            <p class="mb-0 text-muted small">{{ __('No outstanding cybersecurity gaps — you are all caught up.') }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-muted small mb-0">{{ __('Complete every action in your Action Plan to earn this badge.') }}</p>
                @endif
            </div>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Profile Information') }}</h5>
                    <p class="text-muted small">{{ __('Update your account name, business name and email address.') }}</p>

                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Name') }}</label>
                            <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus>
                            @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        @if ($user->role === 'sme_owner')
                            <div class="mb-3">
                                <label for="business_name" class="form-label">{{ __('Business Name') }}</label>
                                <input id="business_name" name="business_name" type="text" class="form-control" value="{{ old('business_name', $user->business_name) }}">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ssm_number" class="form-label">{{ __('SSM Number') }}</label>
                                    <input id="ssm_number" name="ssm_number" type="text" class="form-control" value="{{ old('ssm_number', $user->ssm_number) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">{{ __('Phone') }}</label>
                                    <input id="phone" name="phone" type="text" class="form-control" value="{{ old('phone', $user->phone) }}">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="business_type" class="form-label">{{ __('Industry') }}</label>
                                    <select id="business_type" name="business_type" class="form-select">
                                        <option value="">{{ __('Select industry') }}</option>
                                        @foreach ($businessTypes as $type)
                                            <option value="{{ $type }}" {{ old('business_type', $user->business_type) === $type ? 'selected' : '' }}>{{ __($type) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="state" class="form-label">{{ __('State') }}</label>
                                    <select id="state" name="state" class="form-select">
                                        <option value="">{{ __('Select state') }}</option>
                                        @foreach ($states as $state)
                                            <option value="{{ $state }}" {{ old('state', $user->state) === $state ? 'selected' : '' }}>{{ $state }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex align-items-center gap-3">
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                            @if (session('status') === 'profile-updated')
                                <span class="text-success small">{{ __('Saved.') }}</span>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            @if ($user->isSmeOwner())
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('Two-Factor Authentication') }}</h5>
                        <p class="text-muted small">{{ __('When enabled, we\'ll email you a verification code each time you log in, in addition to your password.') }}</p>

                        <form method="POST" action="{{ route('profile.two-factor') }}" class="d-flex align-items-center gap-2">
                            @csrf
                            @method('patch')
                            <input type="hidden" name="two_factor_enabled" value="{{ $user->two_factor_enabled ? '0' : '1' }}">
                            @if ($user->two_factor_enabled)
                                <span class="badge maturity-Optimized">{{ __('Enabled') }}</span>
                                <button type="submit" class="btn btn-outline-secondary btn-sm">{{ __('Disable') }}</button>
                            @else
                                <span class="badge bg-secondary">{{ __('Disabled') }}</span>
                                <button type="submit" class="btn btn-outline-primary btn-sm">{{ __('Enable') }}</button>
                            @endif
                        </form>
                    </div>
                </div>
            @endif

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">{{ __('Update Password') }}</h5>
                    <p class="text-muted small">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>

                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label for="current_password" class="form-label">{{ __('Current Password') }}</label>
                            <input id="current_password" name="current_password" type="password" class="form-control" autocomplete="current-password">
                            @error('current_password', 'updatePassword') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('New Password') }}</label>
                            <input id="password" name="password" type="password" class="form-control" autocomplete="new-password">
                            <div class="form-text">{{ __('At least 8 characters, with uppercase and lowercase letters and a number.') }}</div>
                            @error('password', 'updatePassword') <div class="text-danger small">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">{{ __('Confirm New Password') }}</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm border-danger-subtle">
                <div class="card-body">
                    <h5 class="card-title text-danger">{{ __('Delete Account') }}</h5>
                    <p class="text-muted small">{{ __('Once your account is deleted, all of its data will be permanently removed.') }}</p>

                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        {{ __('Delete Account') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Delete Account') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ __('Are you sure you want to delete your account? This cannot be undone.') }}</p>
                        <label for="delete_password" class="form-label">{{ __('Password') }}</label>
                        <input id="delete_password" name="password" type="password" class="form-control" placeholder="{{ __('Enter your password to confirm') }}">
                        @error('password', 'userDeletion') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('Delete Account') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
