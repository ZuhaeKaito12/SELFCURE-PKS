<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SELFCURE PKS') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-mark.png') }}">
    <style>
        body { background: var(--sp-bg); }

        .guest-split { display: grid; grid-template-columns: 1.05fr 1fr; gap: 12px; min-height: 100vh; min-height: 100dvh; padding: 12px; }
        @media (max-width: 991.98px) { .guest-split { grid-template-columns: 1fr; padding: 0; } }

        /* Fallback chain: video -> poster image -> navy. */
        .guest-art {
            position: sticky; top: 12px; height: calc(100vh - 24px); height: calc(100dvh - 24px);
            overflow: hidden; border-radius: var(--sp-radius-lg); color: #fff; padding: 2.5rem;
            flex-direction: column; justify-content: space-between;
            background: #0b1224 url('{{ asset('media/login-bg-poster.jpg') }}') center / cover no-repeat;
        }
        .guest-art video { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
        .guest-bg-overlay, .guest-bg-overlay-admin { position: absolute; inset: 0; }
        .guest-bg-overlay { background: linear-gradient(180deg, rgba(8, 15, 33, .35) 0%, rgba(8, 15, 33, .2) 40%, rgba(8, 15, 33, .85) 100%); }
        .guest-bg-overlay-admin { background: rgba(2, 6, 23, .35); opacity: 0; transition: opacity 300ms ease; }
        body.guest-bg-admin .guest-bg-overlay-admin { opacity: 1; }
        .guest-art > .guest-art-content { position: relative; z-index: 1; display: flex; flex-direction: column; justify-content: space-between; height: 100%; }

        .guest-brand { display: flex; align-items: center; gap: .65rem; }
        .guest-brand img { width: 42px; height: 42px; padding: 4px; border-radius: 11px; background: #fff; object-fit: contain; }
        .guest-brand strong { display: block; font-size: .95rem; }
        .guest-brand span { display: block; font-family: var(--sp-mono); font-size: .7rem; color: #9fb0cf; }
        .guest-pitch { max-width: 480px; }
        .guest-pitch h1 { color: #fff; font-size: 2.75rem; letter-spacing: -0.045em; line-height: 1.05; }
        .guest-pitch p { color: #c3cde0; margin: 1rem 0 0; font-size: 1rem; max-width: 44ch; }
        .guest-facts { display: flex; gap: 1.75rem; margin-top: 1.75rem; padding-top: 1.4rem; border-top: 1px solid rgba(255, 255, 255, .14); }
        .guest-facts b { display: block; font-size: 1.5rem; font-weight: 700; letter-spacing: -0.03em; }
        .guest-facts span { font-family: var(--sp-mono); font-size: .75rem; color: #9fb0cf; }

        .guest-main { position: relative; display: grid; place-items: center; padding: 4.5rem 1.25rem 2.5rem; }
        .guest-lang { position: absolute; top: 1.25rem; right: 1.25rem; }
        .guest-form { width: 100%; max-width: 420px; animation: guest-in 300ms var(--ease-out) backwards; }

        .guest-logo-wrap { position: relative; height: 56px; width: 56px; margin-bottom: 1rem; }
        .guest-logo-wrap img {
            position: absolute; inset: 0; width: 56px; height: 56px; object-fit: contain;
            transition: opacity 250ms var(--ease-out), transform 250ms var(--ease-out);
        }
        .guest-logo-wrap img.logo-active { opacity: 1; transform: none; }
        .guest-logo-wrap img.logo-inactive { opacity: 0; transform: translateY(6px); pointer-events: none; }
        .guest-tagline { font-family: var(--sp-mono); font-size: .75rem; color: var(--sp-blue); }

        @keyframes guest-in { from { opacity: 0; transform: translateY(8px); } }

        @media (prefers-reduced-motion: reduce) {
            .guest-form { animation-name: sp-fade; }
            .guest-logo-wrap img.logo-inactive { transform: none; }
            .guest-art video { display: none; }
        }
    </style>
</head>
<body>
    <div class="guest-split">
        <aside class="guest-art d-none d-lg-flex" aria-hidden="true">
            <video id="guestBgVideo" autoplay muted loop playsinline preload="auto" poster="{{ asset('media/login-bg-poster.jpg') }}">
                <source src="{{ asset('media/login-bg.mp4') }}" type="video/mp4">
            </video>
            <div class="guest-bg-overlay"></div>
            <div class="guest-bg-overlay-admin"></div>
            <div class="guest-art-content">
                <div class="guest-brand">
                    <img src="{{ asset('images/logo-mark.png') }}" alt="">
                    <div><strong>SELFCURE PKS</strong><span>{{ __('cyber self-assessment') }}</span></div>
                </div>
                <div class="guest-pitch">
                    <h1>{{ __('Know where your business stands on cybersecurity.') }}</h1>
                    <p>{{ __('A short self-assessment built for Malaysian SMEs. Get a clear score, see what to fix first, and follow a step-by-step action plan.') }}</p>
                    <div class="guest-facts">
                        <div><b>17</b><span>{{ __('questions') }}</span></div>
                        <div><b>5</b><span>{{ __('NIST CSF domains') }}</span></div>
                        <div><b>~10 min</b><span>{{ __('with autosave') }}</span></div>
                    </div>
                </div>
            </div>
        </aside>

        <main class="guest-main">
            <div class="guest-lang"><x-language-toggle variant="light" /></div>
            <div class="guest-form">
                <a href="{{ route('home') }}" class="d-inline-block text-decoration-none" aria-label="SELFCURE PKS">
                    <div class="guest-logo-wrap">
                        <img id="guestLogoSme" src="{{ asset('images/logo-mark.png') }}" alt="SELFCURE PKS" class="logo-active">
                        <img id="guestLogoAdmin" src="{{ asset('images/admin-logo-mark.png') }}" alt="SELFCURE ADMIN" class="logo-inactive">
                    </div>
                </a>
                <p id="guestTagline" class="guest-tagline mb-2">{{ __('Cybersecurity self-assessment for Malaysian SMEs') }}</p>
                {{ $slot }}
            </div>
        </main>
    </div>

    <script>
        (function () {
            const video = document.getElementById('guestBgVideo');
            if (!video) return;
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                video.removeAttribute('autoplay');
                video.pause();
            }
            video.addEventListener('error', () => video.remove(), true);
        })();
    </script>
</body>
</html>
