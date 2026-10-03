<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-outline-secondary btn-sm mb-3">&larr; {{ __('Back to Client') }}</a>
        <h2 class="fs-4 fw-semibold mb-0">{{ __('Edit Client') }} — {{ $client->business_name ?? $client->name }}</h2>
    </x-slot>

    <div class="card shadow-sm col-lg-7">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.clients.update', $client) }}">
                @csrf
                @method('put')

                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('Owner Name') }}</label>
                    <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $client->name) }}" required autofocus>
                    @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="business_name" class="form-label">{{ __('Business Name') }}</label>
                    <input id="business_name" name="business_name" type="text" class="form-control" value="{{ old('business_name', $client->business_name) }}" required>
                    @error('business_name') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="ssm_number" class="form-label">{{ __('SSM Number') }}</label>
                        <input id="ssm_number" name="ssm_number" type="text" class="form-control" value="{{ old('ssm_number', $client->ssm_number) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">{{ __('Phone') }}</label>
                        <input id="phone" name="phone" type="text" class="form-control" value="{{ old('phone', $client->phone) }}">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="business_type" class="form-label">{{ __('Industry') }}</label>
                        <select id="business_type" name="business_type" class="form-select">
                            <option value="">{{ __('Select industry') }}</option>
                            @foreach ($businessTypes as $type)
                                <option value="{{ $type }}" {{ old('business_type', $client->business_type) === $type ? 'selected' : '' }}>{{ __($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="state" class="form-label">{{ __('State') }}</label>
                        <select id="state" name="state" class="form-select">
                            <option value="">{{ __('Select state') }}</option>
                            @foreach ($states as $state)
                                <option value="{{ $state }}" {{ old('state', $client->state) === $state ? 'selected' : '' }}>{{ $state }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Email') }}</label>
                    <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $client->email) }}" required>
                    @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                    <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-outline-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm col-lg-7 mt-4 border-danger-subtle">
        <div class="card-body">
            <h5 class="text-danger">{{ __('Remove Client') }}</h5>
            <p class="text-muted small">{{ __('This will permanently delete the client account and all of their assessment records.') }}</p>
            <form method="POST" action="{{ route('admin.clients.destroy', $client) }}" onsubmit="return confirm({{ Js::from(__('Are you sure you want to remove this client? This cannot be undone.')) }});">
                @csrf
                @method('delete')
                <button type="submit" class="btn btn-outline-danger btn-sm">{{ __('Delete Client Account') }}</button>
            </form>
        </div>
    </div>
</x-app-layout>
