<x-guest-layout>
    <h1 class="fs-2 fw-bold mb-1" style="letter-spacing: -0.035em;">{{ __('Welcome back') }}</h1>
    <p class="text-muted mb-4">{{ __('Sign in to continue to your portal.') }}</p>

    <ul class="nav nav-pills nav-fill mb-4" id="loginRoleTabs">
        <li class="nav-item">
            <button type="button" class="nav-link {{ old('portal', 'sme_owner') === 'sme_owner' ? 'active' : '' }}" data-role-tab="sme_owner">{{ __('SME Owner') }}</button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link {{ old('portal') === 'admin' ? 'active' : '' }}" data-role-tab="admin">{{ __('Admin') }}</button>
        </li>
    </ul>

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

    <p id="loginRoleHint" class="text-muted small">{{ __('Logging in as an') }} <strong>{{ old('portal') === 'admin' ? __('Administrator') : __('SME Owner') }}</strong>.</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <input type="hidden" name="portal" id="portal" value="{{ old('portal', 'sme_owner') }}">

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus autocomplete="username">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input id="password" type="password" name="password" class="form-control" required autocomplete="current-password">
        </div>

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="form-check mb-0">
                <input type="checkbox" name="remember" id="remember_me" class="form-check-input">
                <label for="remember_me" class="form-check-label">{{ __('Remember me') }}</label>
            </div>
            @if (Route::has('password.request'))
                <a class="small fw-semibold" href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary btn-lg w-100">{{ __('Enter Portal') }}</button>

        <p class="text-center small text-muted mt-4 mb-0">
            {{ __("Don't have an account?") }} <a href="{{ route('register') }}" class="fw-semibold">{{ __('Register as an SME Owner') }}</a>
        </p>    </form>

    <script>
        const loginRoleLabels = {
            admin: {{ Js::from(__('Administrator')) }},
            sme_owner: {{ Js::from(__('SME Owner')) }},
        };
        const loggingInAsText = {{ Js::from(__('Logging in as an')) }};

        document.querySelectorAll('[data-role-tab]').forEach((btn) => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('[data-role-tab]').forEach((b) => b.classList.remove('active'));
                btn.classList.add('active');
                const role = btn.dataset.roleTab;
                const label = loginRoleLabels[role];
                document.getElementById('loginRoleHint').innerHTML = loggingInAsText + ' <strong>' + label + '</strong>.';
                document.getElementById('portal').value = role;

                setActiveLogo(role);
            });
        });

        function setActiveLogo(role) {
            const sme = document.getElementById('guestLogoSme');
            const admin = document.getElementById('guestLogoAdmin');
            sme.classList.toggle('logo-active', role !== 'admin');
            sme.classList.toggle('logo-inactive', role === 'admin');
            admin.classList.toggle('logo-active', role === 'admin');
            admin.classList.toggle('logo-inactive', role !== 'admin');

            document.body.classList.toggle('guest-bg-admin', role === 'admin');
        }

        // Reflect the tab that's already active on page load (e.g. after a
        // failed admin login attempt, the Admin tab re-renders as active).
        const initialRole = document.querySelector('[data-role-tab].active')?.dataset.roleTab;
        if (initialRole === 'admin') {
            setActiveLogo('admin');
        }
    </script>
</x-guest-layout>
