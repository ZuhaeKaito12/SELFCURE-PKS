<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SELFCURE PKS — {{ __('Cybersecurity Self-Assessment for Malaysian SMEs') }}</title>
    <meta name="description" content="{{ __('Free self-assessment for Malaysian SMEs. No IT background needed.') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/theme.css') }}?v={{ filemtime(public_path('css/theme.css')) }}" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-mark.png') }}">
    <style>
        .home-nav { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1rem 0; }
        .home-brand { display: flex; align-items: center; gap: .6rem; color: var(--sp-ink); font-weight: 700; }
        .home-brand img { width: 38px; height: 38px; object-fit: contain; }
        .home-brand:hover { color: var(--sp-ink); }
        .home-nav .btn { white-space: nowrap; }
        /* Phones: the hero already has a Log in button, so the nav keeps just the essentials. */
        @media (max-width: 575.98px) {
            .home-nav .home-login { display: none; }
            .home-brand { font-size: .95rem; }
        }

        /* Fallback chain: video -> poster image -> navy. */
        .home-hero {
            position: relative; overflow: hidden; border-radius: var(--sp-radius-lg); color: #fff;
            padding: clamp(2.5rem, 7vw, 5.5rem) clamp(1.5rem, 5vw, 4rem);
            background: #0b1224 url('{{ asset('media/login-bg-poster.jpg') }}') center / cover no-repeat;
        }
        .home-hero video { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
        .home-hero::before { content: ""; position: absolute; inset: 0; z-index: 1; background: linear-gradient(90deg, rgba(8, 15, 33, .88) 0%, rgba(8, 15, 33, .55) 60%, rgba(8, 15, 33, .25) 100%); }
        .home-hero > .home-hero-content { position: relative; z-index: 2; max-width: 620px; }
        .home-hero .badge-tracked { color: #8fb0ff; }
        .home-hero h1 { color: #fff; font-size: clamp(2.2rem, 5vw, 3.6rem); letter-spacing: -0.045em; line-height: 1.04; margin: .8rem 0 1rem; }
        .home-hero p { color: #c3cde0; font-size: 1.1rem; max-width: 46ch; }
        .home-hero .btn-light { background: #fff; color: var(--sp-ink); border: 0; }
        .home-hero .btn-ghost { color: #fff; background: rgba(255, 255, 255, .1); border: 0; }
        .home-hero .btn-ghost:hover { color: #fff; background: rgba(255, 255, 255, .18); }
        .home-facts { display: flex; flex-wrap: wrap; gap: 1.5rem 2.25rem; margin-top: 2.25rem; padding-top: 1.5rem; border-top: 1px solid rgba(255, 255, 255, .14); }
        .home-facts b { display: block; font-size: 1.6rem; letter-spacing: -0.03em; }
        .home-facts span { font-family: var(--sp-mono); font-size: .75rem; color: #9fb0cf; }

        .home-steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin: 3rem 0 1rem; }
        .home-step { padding: 1.5rem; border-radius: var(--sp-radius-lg); background: var(--sp-surface); box-shadow: var(--sp-shadow-sm); }
        .home-step-n { display: grid; place-items: center; width: 34px; height: 34px; border-radius: 10px; background: var(--sp-blue-soft); color: var(--sp-blue); font-family: var(--sp-mono); font-weight: 600; margin-bottom: 1rem; }
        .home-step h2 { font-size: 1.05rem; margin: .35rem 0 .4rem; }
        .home-step p { color: var(--sp-ink-soft); margin: 0; font-size: .92rem; }
        @media (max-width: 767.98px) { .home-steps { grid-template-columns: 1fr; } }

        .home-footer { padding: 2rem 0 2.5rem; color: var(--sp-ink-faint); font-size: .85rem; text-align: center; }
        @media (prefers-reduced-motion: reduce) { .home-hero video { display: none; } }
    </style>
</head>
<body>
    <div class="container" style="max-width: 1200px;">
        <nav class="home-nav" aria-label="{{ __('Main') }}">
            <a href="{{ route('home') }}" class="home-brand text-decoration-none">
                <img src="{{ asset('images/logo-mark.png') }}" alt="">
                SELFCURE PKS
            </a>
            <div class="d-flex align-items-center gap-2">
                <x-language-toggle variant="light" />
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">{{ __('Dashboard') }}</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm home-login">{{ __('Log in') }}</a>
                    <a href="{{ route('register') }}" class="btn btn-primary btn-sm">{{ __('Register') }}</a>
                @endauth
            </div>
        </nav>

        <main>
            <header class="home-hero">
                <video autoplay muted loop playsinline poster="{{ asset('media/login-bg-poster.jpg') }}" aria-hidden="true">
                    <source src="{{ asset('media/login-bg.mp4') }}" type="video/mp4">
                </video>
                <div class="home-hero-content">
                    <span class="badge-tracked">{{ __('Free Cybersecurity Self-Assessment') }}</span>
                    <h1>{{ __('Know Your Cyber Readiness In Minutes') }}</h1>
                    <p>{{ __('Free self-assessment for Malaysian SMEs. No IT background needed.') }}</p>
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg">{{ __('Go to Dashboard') }}</a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-light btn-lg">{{ __('Start Your Free Assessment') }}</a>
                            <a href="{{ route('login') }}" class="btn btn-ghost btn-lg">{{ __('Log in') }}</a>
                        @endauth
                    </div>
                    <div class="home-facts">
                        <div><b>17</b><span>{{ __('questions') }}</span></div>
                        <div><b>5</b><span>{{ __('NIST CSF domains') }}</span></div>
                        <div><b>~10 min</b><span>{{ __('with autosave') }}</span></div>
                    </div>
                </div>
            </header>

            <section class="home-steps" aria-label="{{ __('How it works') }}">
                <article class="home-step">
                    <span class="home-step-n">1</span>
                    <span class="badge-tracked text-muted">{{ __('Real-Time Scoring') }}</span>
                    <h2>{{ __('17-Question Self-Assessment') }}</h2>
                    <p>{{ __('17 simple questions with autosave. Takes 10-15 minutes.') }}</p>
                </article>
                <article class="home-step">
                    <span class="home-step-n">2</span>
                    <span class="badge-tracked text-muted">{{ __('Visual Analytics') }}</span>
                    <h2>{{ __('Radar & Gap Analysis') }}</h2>
                    <p>{{ __('A radar and gap chart show exactly where to focus first.') }}</p>
                </article>
                <article class="home-step">
                    <span class="home-step-n">3</span>
                    <span class="badge-tracked text-muted">{{ __('Downloadable') }}</span>
                    <h2>{{ __('Prioritised PDF Report') }}</h2>
                    <p>{{ __('A clear PDF report with prioritised recommendations.') }}</p>
                </article>
            </section>
        </main>

        <footer class="home-footer">
            SELFCURE PKS &mdash; {{ __('Final Year Project, Universiti Poly-Tech Malaysia') }}
        </footer>
    </div>
</body>
</html>
