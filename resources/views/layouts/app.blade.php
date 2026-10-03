<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'SELFCURE PKS') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/intro.js@8.6.0/minified/introjs.min.css" rel="stylesheet">
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-mark.png') }}">
</head>
@php $isAdmin = auth()->check() && auth()->user()->isAdmin(); @endphp
<body class="{{ $isAdmin ? 'theme-admin' : 'theme-sme' }}">
    <a class="sp-skip" href="#mainContent">{{ __('Skip to content') }}</a>
    @php
        // Stroke icons for the sidebar, drawn at one weight.
        $navIcons = [
            'dashboard' => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>',
            'history' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
            'plan' => '<path d="M9 6h11M9 12h11M9 18h11"/><path d="m3 6 1 1 2-2M3 12l1 1 2-2M3 18l1 1 2-2"/>',
            'guide' => '<path d="M4 5a2 2 0 0 1 2-2h12v16H6a2 2 0 0 0-2 2z"/><path d="M4 19V5M8 7h6"/>',
            'directory' => '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 10h18M9 10v10"/>',
            'records' => '<path d="M9 4h6a1 1 0 0 1 1 1v1H8V5a1 1 0 0 1 1-1Z"/><path d="M16 5h2a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h2"/><path d="m9 13 2 2 4-4"/>',
        ];
        $navIcon = fn ($key) => '<svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$navIcons[$key].'</svg>';
    @endphp
    <div class="d-flex app-shell">
        <div class="offcanvas-lg offcanvas-start app-sidebar" tabindex="-1" id="appSidebar" aria-label="{{ __('Menu') }}">
            <div class="offcanvas-header d-lg-none">
                <span class="fw-semibold">{{ __('Menu') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#appSidebar" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column p-0">
                <a href="{{ route('dashboard') }}" class="sidebar-brand text-decoration-none">
                    @if ($isAdmin)
                        <img src="{{ asset('images/admin-logo-mark.png') }}" alt="">
                        <span><strong>SELFCURE ADMIN</strong><span class="sidebar-role-badge">{{ __('Administrator') }}</span></span>
                    @else
                        <img src="{{ asset('images/logo-mark.png') }}" alt="">
                        <span><strong>SELFCURE PKS</strong><span class="sidebar-role-badge">{{ __('SME Owner') }}</span></span>
                    @endif
                </a>

                <nav class="sidebar-nav" aria-label="{{ __('Main') }}">
                    @auth
                        @if ($isAdmin)
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') || request()->routeIs('admin.clients.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}" @if (request()->routeIs('admin.dashboard') || request()->routeIs('admin.clients.*')) aria-current="page" @endif>
                                {!! $navIcon('directory') !!} {{ __('SME Directory') }}
                            </a>
                            <a class="nav-link {{ request()->routeIs('admin.assessments.*') ? 'active' : '' }}" href="{{ route('admin.assessments.index') }}" @if (request()->routeIs('admin.assessments.*')) aria-current="page" @endif>
                                {!! $navIcon('records') !!} {{ __('Assessment Records') }}
                            </a>
                        @else
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" @if (request()->routeIs('dashboard')) aria-current="page" @endif>
                                {!! $navIcon('dashboard') !!} {{ __('Dashboard') }}
                            </a>
                            <a class="nav-link {{ request()->routeIs('assessment.history') ? 'active' : '' }}" href="{{ route('assessment.history') }}" @if (request()->routeIs('assessment.history')) aria-current="page" @endif>
                                {!! $navIcon('history') !!} {{ __('Assessment History') }}
                            </a>
                            <a class="nav-link {{ request()->routeIs('action-plan.*') ? 'active' : '' }}" href="{{ route('action-plan.index') }}" @if (request()->routeIs('action-plan.*')) aria-current="page" @endif>
                                {!! $navIcon('plan') !!} {{ __('Action Plan') }}
                            </a>
                        @endif
                        <a class="nav-link {{ request()->routeIs('help.index') ? 'active' : '' }}" href="{{ route('help.index') }}" @if (request()->routeIs('help.index')) aria-current="page" @endif>
                            {!! $navIcon('guide') !!} {{ __('User Guide') }}
                        </a>
                    @endauth
                </nav>

                @if (auth()->check() && ! $isAdmin)
                    <div class="sidebar-help">
                        <b>{{ __('New to cybersecurity?') }}</b>
                        <p>{{ __('The guided tour shows you around in under a minute.') }}</p>
                        <a href="{{ route('dashboard') }}?tour=1" class="btn btn-outline-primary btn-sm w-100">{{ __('Replay Tour') }}</a>
                    </div>
                @elseif ($isAdmin)
                    <div class="sidebar-help">
                        <a href="{{ route('admin.dashboard') }}?tour=1" class="btn btn-outline-primary btn-sm w-100">{{ __('Replay Tour') }}</a>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex-grow-1 app-content-wrap">
          <div class="app-canvas">
            <div class="app-topbar d-flex align-items-center justify-content-between gap-2">
                <button class="topbar-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#appSidebar" aria-label="{{ __('Menu') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
                </button>
                <span class="topbar-title d-none d-lg-block text-truncate">
                    @auth
                        <b>{{ $isAdmin ? __('Administrator Portal') : (auth()->user()->business_name ?? auth()->user()->name) }}</b>
                        @if ($isAdmin)
                            <span class="admin-role-chip">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>{{ __('Admin access') }}
                            </span>
                        @endif
                    @endauth
                </span>
                <div class="d-flex align-items-center gap-2">
                    @auth
                        <button type="button" class="topbar-btn" id="spTourBtn" hidden onclick="sp_startTour()" aria-label="{{ __('Show a tour of this page') }}" title="{{ __('Show a tour of this page') }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M9.6 9.2a2.5 2.5 0 0 1 4.8.9c0 1.7-2.4 2.2-2.4 3.6"/><path d="M12 17h.01"/></svg>
                        </button>
                    @endauth
                    <span id="tourLanguage"><x-language-toggle variant="light" /></span>
                    @auth
                        <div class="dropdown" id="tourNotifications">
                            @php
                                $notifications = auth()->user()->notifications()->latest()->take(8)->get();
                                $unreadCount = auth()->user()->unreadNotifications()->count();
                            @endphp
                            <a class="topbar-btn" href="#" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ __('Notifications') }}{{ $unreadCount > 0 ? ' ('.$unreadCount.')' : '' }}" title="{{ __('Notifications') }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 8a6 6 0 1 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                                @if ($unreadCount > 0)
                                    <span class="topbar-dot">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                                @endif
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" style="min-width: 320px;">
                                @if ($notifications->isEmpty())
                                    <li><span class="dropdown-item-text text-muted small">{{ __('No notifications yet.') }}</span></li>
                                @else
                                    <li class="d-flex justify-content-end px-3 pb-1">
                                        <form method="POST" action="{{ route('notifications.readAll') }}">
                                            @csrf
                                            <button type="submit" class="btn btn-link btn-sm p-0">{{ __('Mark all as read') }}</button>
                                        </form>
                                    </li>
                                    @foreach ($notifications as $notification)
                                        <li>
                                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item small {{ is_null($notification->read_at) ? 'fw-semibold' : 'text-muted' }}" style="white-space: normal;">
                                                    {{ ($notification->data['is_raw'] ?? false) ? $notification->data['message_key'] : __($notification->data['message_key'] ?? '') }}
                                                    <div class="text-muted" style="font-size: .7rem;">{{ $notification->created_at->diffForHumans() }}</div>
                                                </button>
                                            </form>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                        <div class="dropdown" id="tourProfile">
                            <a class="topbar-avatar dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="avatar-mark" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
                                <span class="d-none d-xl-inline text-truncate" style="max-width: 180px;">{{ auth()->user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><span class="dropdown-item-text small text-muted">{{ auth()->user()->email }}</span></li>
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">{{ __('Profile') }}</a></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">{{ __('Log Out') }}</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm">{{ __('Log in') }}</a>
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">{{ __('Register') }}</a>
                    @endauth
                </div>
            </div>

            <main class="app-content" id="mainContent" tabindex="-1">
                @isset($header)
                    <div class="app-header-bar">
                        {{ $header }}
                    </div>
                @endisset

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
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

                {{ $slot }}
            </main>
          </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intro.js@8.6.0/minified/intro.min.js"></script>
    <script>
        const sp_reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        Chart.defaults.font.family = "'Geist', 'Segoe UI', sans-serif";
        Chart.defaults.color = '#475467';
        Chart.defaults.borderColor = '#e1e6ed';
        Chart.defaults.animation = sp_reduceMotion ? false : { duration: 500, easing: 'easeOutQuart' };

        /*
         * Guided tours. Each page registers its own steps with sp_registerTour().
         * - The main (dashboard) tour starts on first login; "seen" is saved on the server.
         * - Other page tours start on the first visit after that; "seen" is kept in this browser.
         * - ?tour=1 in the URL, or the "?" button in the top bar, replays the current page's tour.
         * A step's `element` can be a list of selectors: the first one visible on screen is used,
         * so a step can point at the sidebar on desktop and the menu button on a phone.
         */
        const SP_TOUR = {
            current: null,
            mainSeen: {{ Js::from((bool) auth()->user()?->has_seen_tour) }},
            storageKey: (key) => 'sp_tour_seen_{{ auth()->id() }}_' + key,
        };

        function sp_tourSeenLocally(key) {
            try { return localStorage.getItem(SP_TOUR.storageKey(key)) === '1'; } catch (e) { return false; }
        }

        function sp_markTourSeen(tour) {
            if (tour.opts.persist === 'server') {
                SP_TOUR.mainSeen = true;
                fetch({{ Js::from(route('tour.complete')) }}, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': {{ Js::from(csrf_token()) }}, 'Accept': 'application/json' },
                });
            } else {
                try { localStorage.setItem(SP_TOUR.storageKey(tour.key), '1'); } catch (e) {}
            }
        }

        // Visible means on screen and not hidden, e.g. not the off-canvas sidebar on a phone.
        function sp_isVisible(el) {
            if (!el) return false;
            const r = el.getBoundingClientRect();
            return r.width > 0 && r.height > 0 && r.right > 0 && r.left < window.innerWidth
                && getComputedStyle(el).visibility !== 'hidden';
        }

        function sp_resolveSteps(steps) {
            return steps.map((step) => {
                if (!step.element) return { ...step };
                const target = [].concat(step.element)
                    .map((selector) => document.querySelector(selector))
                    .find(sp_isVisible);
                return target ? { ...step, element: target } : null;
            }).filter(Boolean);
        }

        function sp_startTour() {
            const tour = SP_TOUR.current;
            if (!tour) return;
            const steps = sp_resolveSteps(tour.steps);
            if (steps.length === 0) return;

            const intro = (introJs.tour ? introJs.tour() : introJs());
            intro.setOptions({
                steps,
                tooltipClass: 'sp-tour',
                highlightClass: 'sp-tour-highlight',
                showStepNumbers: true,
                stepNumbersOfLabel: {{ Js::from(__('of')) }},
                showProgress: false,
                showBullets: false,
                hidePrev: true,
                exitOnOverlayClick: false,
                exitOnEsc: true,
                keyboardNavigation: true,
                disableInteraction: true,
                scrollToElement: true,
                scrollPadding: 120,
                helperElementPadding: 8,
                nextLabel: {{ Js::from(__('Next')) }},
                prevLabel: {{ Js::from(__('Back')) }},
                doneLabel: {{ Js::from(__('Finish')) }},
                skipLabel: {{ Js::from(__('Skip tour')) }},
            });
            let marked = false;
            const done = () => { if (!marked) { marked = true; sp_markTourSeen(tour); } };
            (intro.onComplete || intro.oncomplete).call(intro, done);
            (intro.onExit || intro.onexit).call(intro, done);
            // Let the page's entrance animation finish so highlights land on settled positions.
            setTimeout(() => intro.start(), sp_reduceMotion ? 0 : 450);
        }

        function sp_registerTour(key, steps, opts = {}) {
            SP_TOUR.current = { key, steps, opts };
            const button = document.getElementById('spTourBtn');
            if (button) button.hidden = false;

            const forced = new URLSearchParams(window.location.search).get('tour') === '1';
            const auto = opts.persist === 'server'
                ? !SP_TOUR.mainSeen
                : SP_TOUR.mainSeen && !sp_tourSeenLocally(key);
            if (forced || auto) sp_startTour();
        }

        // Kept for pages that start a tour directly (the administrator dashboard).
        function sp_runTour(steps) {
            SP_TOUR.current = { key: 'main', steps, opts: { persist: 'server' } };
            const button = document.getElementById('spTourBtn');
            if (button) button.hidden = false;
            sp_startTour();
        }
    </script>
    {{ $scripts ?? '' }}
</body>
</html>
