<x-app-layout>
    <x-slot name="header">
        <div>
            <span class="badge-tracked maturity-Developing d-inline-block mb-2">{{ __('Action Roadmap') }}</span>
            <h2 class="fs-3 fw-bold mb-0">{{ __('What to work on next') }}</h2>
            <p class="text-muted mb-0">{{ __('Practical actions based on the gaps found in your assessment. Tick off each step as you go.') }}</p>
        </div>
    </x-slot>

    <div class="card mb-4" id="tourPlanSummary">
        <div class="card-body d-flex align-items-center gap-4 flex-wrap">
            <div class="flex-grow-1" style="min-width: 260px;">
                <span class="fw-semibold">
                    {!! __(':completed of :total actions completed', [
                        'completed' => '<span class="js-sum-completed sp-num">'.$summary['completed'].'</span>',
                        'total' => '<span class="js-sum-total sp-num">'.$summary['total'].'</span>',
                    ]) !!}
                </span>
                <div class="plan-stack" aria-hidden="true">
                    <i class="js-stack-completed" style="flex-grow: {{ $summary['completed'] }}; background: var(--sp-good);"></i>
                    <i class="js-stack-in_progress" style="flex-grow: {{ $summary['in_progress'] }}; background: var(--sp-managed);"></i>
                    <i class="js-stack-not_started" style="flex-grow: {{ $summary['not_started'] }}; background: var(--sp-border-strong);"></i>
                </div>
                <div class="d-flex flex-wrap gap-2 small">
                    <span class="badge status-badge status-completed">{{ __('Completed') }}: <span class="js-sum-completed">{{ $summary['completed'] }}</span></span>
                    <span class="badge status-badge status-in_progress">{{ __('In Progress') }}: <span class="js-sum-in_progress">{{ $summary['in_progress'] }}</span></span>
                    <span class="badge status-badge status-not_started">{{ __('Not Started') }}: <span class="js-sum-not_started">{{ $summary['not_started'] }}</span></span>
                </div>
            </div>
            <div class="plan-big sp-num"><span class="js-sum-percent">{{ $summary['percent'] }}</span>%<small>{{ __('complete') }}</small></div>
        </div>
    </div>

    @if ($summary['total'] === 0)
        <div class="card">
            <div class="card-body empty-state">
                <span class="empty-state-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5 9-10"/></svg></span>
                @if ($hasCompletedAssessment)
                    <h3>{{ __('No gaps found — great job!') }}</h3>
                    <p class="mb-0">{{ __('Your latest assessment found no weak areas to fix. Keep reviewing your practices periodically.') }}</p>
                @else
                    <h3>{{ __('No actions yet.') }}</h3>
                    <p>{{ __('Complete a self-assessment and we will turn any gaps into practical, step-by-step actions here.') }}</p>
                    <a href="{{ route('assessment.start') }}" class="btn btn-primary">{{ __('Start Assessment') }}</a>
                @endif
            </div>
        </div>
    @else
        @if ($openItems->isNotEmpty())
            <h2 class="roadmap-section-title">{{ __('To do') }} ({{ $openItems->count() }})</h2>
            <div class="d-grid gap-2 mb-4">
                @foreach ($openItems as $item)
                    @include('action-plan._card', ['item' => $item])
                @endforeach
            </div>
        @endif

        @if ($completedItems->isNotEmpty())
            <h2 class="roadmap-section-title">{{ __('Completed') }} ({{ $completedItems->count() }})</h2>
            <div class="d-grid gap-2 mb-4">
                @foreach ($completedItems as $item)
                    @include('action-plan._card', ['item' => $item])
                @endforeach
            </div>
        @endif
    @endif

    <a href="{{ route('dashboard') }}" class="btn btn-link">&larr; {{ __('Back to Dashboard') }}</a>

    <div class="modal fade" id="achievementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="achievement-badge mx-auto" style="max-width: 320px;">
                        <div class="achievement-badge-icon">✓</div>
                        <div class="text-start">
                            <div class="achievement-badge-title">{{ __('Action Roadmap Complete') }}</div>
                            <p class="mb-0 text-muted small">{{ __('You have completed every action in your roadmap. Retake the assessment to see your improved score.') }}</p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <a href="{{ route('assessment.start') }}" class="btn btn-outline-primary">{{ __('Retake Assessment') }}</a>
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <script>
            (function () {
                const csrf = {{ Js::from(csrf_token()) }};
                const statusLabels = {
                    not_started: {{ Js::from(__('Not Started')) }},
                    in_progress: {{ Js::from(__('In Progress')) }},
                    completed: {{ Js::from(__('Completed')) }},
                };
                const showAchievement = () => new bootstrap.Modal(document.getElementById('achievementModal')).show();

                @if (session('achievement_unlocked'))
                    document.addEventListener('DOMContentLoaded', showAchievement);
                @endif

                function send(url, body) {
                    return fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                        },
                        body: JSON.stringify(body),
                    }).then((r) => {
                        if (!r.ok) throw new Error('Request failed');
                        return r.json();
                    });
                }

                function render(card, data) {
                    const wasCompleted = card.dataset.status === 'completed';
                    card.dataset.status = data.status;
                    card.classList.toggle('is-completed', data.status === 'completed');

                    const badge = card.querySelector('.js-status-badge');
                    Object.keys(statusLabels).forEach((s) => badge.classList.remove('status-' + s));
                    badge.classList.add('status-' + data.status);
                    badge.textContent = statusLabels[data.status];

                    card.querySelectorAll('.status-option').forEach((btn) => {
                        const active = btn.value === data.status;
                        btn.classList.toggle('is-active', active);
                        btn.setAttribute('aria-pressed', active ? 'true' : 'false');
                    });

                    card.querySelectorAll('.js-step').forEach((box) => {
                        box.checked = data.done_steps.includes(parseInt(box.dataset.step, 10));
                    });
                    card.querySelector('.js-steps-done').textContent = data.done_steps.length;
                    card.querySelector('.js-steps-done-mini').textContent = data.done_steps.length;
                    card.querySelector('.js-progress').style.transform = 'scaleX(' + data.progress / 100 + ')';
                    card.querySelector('.js-progress-mini').style.transform = 'scaleX(' + data.progress / 100 + ')';

                    const s = data.summary;
                    document.querySelectorAll('.js-sum-completed').forEach((el) => { el.textContent = s.completed; });
                    document.querySelectorAll('.js-sum-total').forEach((el) => { el.textContent = s.total; });
                    document.querySelector('.js-sum-percent').textContent = s.percent;
                    document.querySelector('.js-sum-not_started').textContent = s.not_started;
                    document.querySelector('.js-sum-in_progress').textContent = s.in_progress;
                    ['completed', 'in_progress', 'not_started'].forEach((k) => {
                        document.querySelector('.js-stack-' + k).style.flexGrow = s[k];
                    });

                    if (data.status === 'completed' && !wasCompleted) {
                        card.classList.remove('just-completed');
                        void card.offsetWidth;
                        card.classList.add('just-completed');
                    }
                    if (data.plan_complete) showAchievement();
                }

                document.querySelectorAll('.action-card').forEach((card) => {
                    const form = card.querySelector('.js-status-form');

                    form.addEventListener('submit', (e) => {
                        e.preventDefault();
                        const status = e.submitter ? e.submitter.value : null;
                        if (!status || status === card.dataset.status) return;
                        send(card.dataset.statusUrl, { status })
                            .then((data) => render(card, data))
                            .catch(() => {
                                const input = Object.assign(document.createElement('input'), { type: 'hidden', name: 'status', value: status });
                                form.appendChild(input);
                                HTMLFormElement.prototype.submit.call(form);
                            });
                    });

                    card.querySelectorAll('.js-step').forEach((box) => {
                        box.addEventListener('change', () => {
                            const step = parseInt(box.dataset.step, 10);
                            send(card.dataset.stepsUrl, { step, done: box.checked })
                                .then((data) => render(card, data))
                                .catch(() => { box.checked = !box.checked; });
                        });
                    });
                });

                // One card open at a time; toggled in place so the chevron and bars can animate.
                function setOpen(card, open) {
                    card.classList.toggle('is-open', open);
                    card.querySelector('.js-toggle').setAttribute('aria-expanded', open ? 'true' : 'false');
                }
                document.querySelectorAll('.action-card .js-toggle').forEach((head) => {
                    head.addEventListener('click', () => {
                        const card = head.closest('.action-card');
                        const opening = !card.classList.contains('is-open');
                        document.querySelectorAll('.action-card.is-open').forEach((c) => setOpen(c, false));
                        setOpen(card, opening);
                    });
                });

                // Open the card a link points to, otherwise the first one still to do.
                const target = location.hash ? document.querySelector(location.hash) : null;
                if (target && target.classList.contains('action-card')) {
                    setOpen(target, true);
                    target.classList.add('is-highlighted');
                    target.scrollIntoView({ block: 'start' });
                } else {
                    const first = document.querySelector('.action-card:not(.is-completed)') || document.querySelector('.action-card');
                    if (first) setOpen(first, true);
                }

                @php
                    $tourSteps = [
                        [
                            'element' => '#tourPlanSummary',
                            'title' => __('Your Action Plan'),
                            'intro' => e(__('Every weak area found in your assessment becomes an action here. This bar shows how many you have completed.')),
                        ],
                        [
                            'element' => '.action-card.is-open .js-toggle',
                            'title' => __('One action per card'),
                            'intro' => e(__('Each card is one action, with the most urgent first. Click a card to open or close it.')),
                        ],
                        [
                            'element' => '.action-card.is-open .action-why',
                            'title' => __('Why it matters'),
                            'intro' => e(__('A short explanation of the risk, in plain language.')),
                        ],
                        [
                            'element' => '.action-card.is-open .action-steps',
                            'title' => __('Tick off each step'),
                            'intro' => e(__('Follow the steps and tick each one when it is done. The status changes to In Progress, then Completed, on its own.')),
                        ],
                        [
                            'element' => '.action-card.is-open .status-switch',
                            'title' => __('Change the status yourself'),
                            'intro' => e(__('You can also set the status directly, for example if you have already done this action.')),
                        ],
                        [
                            'element' => '.action-card.is-open .action-source',
                            'title' => __('Based on your answer'),
                            'intro' => e(__('Open this to see which question created this action, your answer, and the related Malaysian regulation.')),
                        ],
                    ];
                @endphp
                sp_registerTour('action-plan', {{ Js::from($tourSteps) }});
            })();
        </script>
    </x-slot>
</x-app-layout>
