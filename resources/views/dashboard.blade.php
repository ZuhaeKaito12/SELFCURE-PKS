<x-app-layout>
    @php
        $arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>';
        $levelColor = ['Initial' => 'var(--sp-critical)', 'Developing' => 'var(--sp-warn)', 'Managed' => 'var(--sp-managed)', 'Optimized' => 'var(--sp-good)'];
        $riskFor = ['Initial' => ['High Risk', 'maturity-Initial'], 'Developing' => ['Medium Risk', 'maturity-Developing'], 'Managed' => ['Low Risk', 'maturity-Optimized'], 'Optimized' => ['Low Risk', 'maturity-Optimized']];
    @endphp

    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3">
            <div>
                <span class="badge-tracked">{{ __('SME Owner Portal') }}</span>
                <h2 class="fw-bold mb-0 mt-2">{{ __('Welcome back,') }} {{ auth()->user()->name }}</h2>
                <p class="text-muted mb-0">{{ $latestAssessment ? __("Here's how your business is doing on cybersecurity.") : __('Start your first self-assessment to see where your business stands.') }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2" id="tourStartAssessment">
                @if ($latestAssessment)
                    <a href="{{ route('assessment.report', $latestAssessment) }}" class="btn btn-outline-secondary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="me-1"><path d="M12 3v12m0 0-4-4m4 4 4-4"/><path d="M4 17v3h16v-3"/></svg>{{ __('PDF Report') }}
                    </a>
                @endif
                @if ($inProgressAssessment)
                    <a href="{{ route('assessment.questionnaire', $inProgressAssessment) }}" class="btn btn-warning">{{ __('Resume Assessment') }}</a>
                @else
                    <a href="{{ route('assessment.start') }}" class="btn btn-primary">{{ $latestAssessment ? __('Take New Assessment') : __('Start Assessment') }}</a>
                @endif
            </div>
        </div>
    </x-slot>

    @if ($reassessmentDue)
        <div class="card admin-note mb-4">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <span class="badge-tracked maturity-Developing d-block mb-1">{{ __('Reassessment Due') }}</span>
                    <p class="mb-0">{{ __('It has been over 6 months since your last assessment. Cyber risks change over time — retake it to make sure your score still reflects reality.') }}</p>
                </div>
                <a href="{{ route('assessment.start') }}" class="btn btn-warning">{{ __('Retake Assessment') }}</a>
            </div>
        </div>
    @endif

    @if ($latestAssessment)
        @php
            $level = $latestAssessment->overall_level;
            [$riskLabel, $riskClass] = $riskFor[$level] ?? ['Low Risk', 'maturity-Optimized'];
            $domainScores = $latestAssessment->domainScores->sortBy('domain.order')->values();
            $lowest = $domainScores->sortBy('score')->values();
            $lowestScore = $lowest->first()?->score;
            $weakest = $lowest->filter(fn ($d) => (float) $d->score === (float) $lowestScore);
        @endphp

        <div class="row g-3 mb-3">
            <div class="col-lg-5" id="tourScoreCard">
                <section class="card score-card h-100">
                    <div class="card-body">
                        <div class="sp-card-head">
                            <h3 class="card-title mb-0">{{ __('Cyber Maturity Score') }}</h3>
                            <a href="{{ route('assessment.result', $latestAssessment) }}" class="sp-link">{{ __('Full result') }} {!! $arrow !!}</a>
                        </div>
                        <div class="score-card-body">
                            <div class="score-gauge">
                                <svg viewBox="0 0 200 110" aria-hidden="true">
                                    <path class="score-gauge-track" d="M15 100 A85 85 0 0 1 185 100" fill="none" stroke-width="16" stroke-linecap="round"/>
                                    <path class="score-gauge-fill" id="scoreGaugeFill" d="M15 100 A85 85 0 0 1 185 100" fill="none" stroke-width="16"
                                          pathLength="100" stroke-dasharray="100" stroke-dashoffset="100"
                                          style="stroke: {{ $levelColor[$level] ?? 'var(--sp-blue)' }};"
                                          data-offset="{{ 100 - (float) $latestAssessment->overall_score }}"/>
                                </svg>
                                <div class="score-gauge-value">
                                    <strong class="sp-num">{{ $latestAssessment->overall_score }}</strong>
                                    <span>{{ __('out of 100') }}</span>
                                </div>
                            </div>
                            <div class="score-meta">
                                <span class="badge sp-dot maturity-{{ $level }}">{{ __($level) }}</span>
                                <span class="badge sp-dot {{ $riskClass }}">{{ __($riskLabel) }}</span>
                            </div>
                        </div>
                        <div class="score-foot">
                            <span>{{ __('Last assessed :date', ['date' => $latestAssessment->completed_at->format('d M Y')]) }}</span>
                            <a href="{{ route('assessment.history') }}" class="text-reset">{{ trans_choice(':count assessment|:count assessments', $completedCount, ['count' => $completedCount]) }}</a>
                        </div>
                    </div>
                </section>
            </div>

            <div class="col-lg-7" id="tourDomainScores">
                <section class="card h-100">
                    <div class="card-body">
                        <div class="sp-card-head">
                            <h3 class="card-title mb-0">{{ __('Score by Domain') }}</h3>
                            <span class="domain-legend d-inline-flex"><i aria-hidden="true"></i>{{ __('Managed level · 65%') }}</span>
                        </div>
                        <div>
                            @foreach ($domainScores as $domainScore)
                                <div class="domain-row">
                                    <span class="domain-icon"><x-domain-icon :code="$domainScore->domain->code" :size="18" /></span>
                                    <span class="domain-name">{{ $domainScore->domain->translatedName() }}</span>
                                    <span class="domain-meter" aria-hidden="true">
                                        <span class="domain-bar"><i style="--w: {{ (float) $domainScore->score / 100 }}; background: {{ $levelColor[$domainScore->level] ?? 'var(--sp-blue)' }};"></i></span>
                                        <span class="domain-bar-mark"></span>
                                    </span>
                                    <span class="domain-score">{{ number_format((float) $domainScore->score, 1) }}%</span>
                                    <span class="badge sp-dot maturity-{{ $domainScore->level }}">{{ __($domainScore->level) }}</span>
                                </div>
                            @endforeach
                        </div>
                        @if ($weakest->isNotEmpty() && (float) $lowestScore < 85)
                            <p class="domain-hint">
                                {{ __('Lowest:') }}
                                <b>{{ $weakest->map(fn ($d) => $d->domain->translatedName())->join(', ', ' '.__('and').' ') }}</b>
                                ({{ number_format((float) $lowestScore, 1) }}%). {{ __('Start there for the biggest improvement.') }}
                            </p>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    @else
        <section class="card mb-3" id="tourScoreCard">
            <div class="card-body empty-state">
                <span class="empty-state-icon"><x-domain-icon code="protect" :size="22" /></span>
                <h3>{{ __('No assessment yet') }}</h3>
                <p>{{ __('Answer 17 short questions across 5 areas. It takes about 10 minutes, and your answers save as you go.') }}</p>
                <a href="{{ $inProgressAssessment ? route('assessment.questionnaire', $inProgressAssessment) : route('assessment.start') }}" class="btn btn-primary">{{ $inProgressAssessment ? __('Resume Assessment') : __('Start Assessment') }}</a>
            </div>
        </section>
    @endif

    <div class="row g-3">
        <div class="col-lg-7" id="tourActionPlan">
            @php $next = $nextActions->first(); @endphp
            @if ($next)
                @php
                    $nextSteps = $next->action->translatedSteps();
                    $nextDone = $next->doneSteps();
                @endphp
                <section class="card next-step-card h-100">
                    <div class="card-body">
                        <span class="badge-tracked">{{ __('Your next step') }}</span>
                        <h3>{{ $next->action->translatedTitle() }}</h3>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge priority-chip priority-{{ $next->priority() }}">{{ $next->priority() === 'high' ? __('High priority') : __('Medium priority') }}</span>
                            <span class="domain-pill">{{ $next->action->question->domain->translatedName() }}</span>
                        </div>
                        <ul class="next-steps">
                            @foreach (array_slice($nextSteps, 0, 3, true) as $index => $step)
                                <li class="{{ in_array($index, $nextDone, true) ? 'is-done' : '' }}">{{ $step }}</li>
                            @endforeach
                        </ul>
                        <div class="next-step-foot">
                            <a href="{{ route('action-plan.index') }}#action-{{ $next->id }}" class="btn btn-primary">{{ __('Continue this action') }}</a>
                            <span class="sp-num">{{ __(':done of :total steps done', ['done' => count($nextDone), 'total' => count($nextSteps)]) }}</span>
                        </div>
                        @if ($nextActions->count() > 1)
                            <div class="next-also">
                                <span class="action-label">{{ __('Up next') }}</span>
                                @foreach ($nextActions->slice(1) as $item)
                                    <a href="{{ route('action-plan.index') }}#action-{{ $item->id }}">
                                        <x-domain-icon :code="$item->action->question->domain->code" :size="16" />
                                        <span class="flex-grow-1">{{ $item->action->translatedTitle() }}</span>
                                        <span class="badge priority-chip priority-{{ $item->priority() }}">{{ $item->priority() === 'high' ? __('High') : __('Medium') }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </section>
            @else
                <section class="card next-step-card h-100">
                    <div class="card-body">
                        <span class="badge-tracked">{{ __('Your next step') }}</span>
                        @if ($actionPlanTotal > 0)
                            <h3>{{ __('Every action is complete') }}</h3>
                            <p>{{ __('Retake the assessment to see how much your score has improved.') }}</p>
                            <a href="{{ route('assessment.start') }}" class="btn btn-primary mt-3">{{ __('Retake Assessment') }}</a>
                        @elseif ($latestAssessment)
                            <h3>{{ __('No gaps found — great job!') }}</h3>
                            <p>{{ __('Your latest assessment found no weak areas to fix. Keep reviewing your practices periodically.') }}</p>
                        @else
                            <h3>{{ __('Your action plan will appear here') }}</h3>
                            <p>{{ __('Complete a self-assessment to generate a personalised action plan from any gaps found.') }}</p>
                        @endif
                    </div>
                </section>
            @endif
        </div>

        <div class="col-lg-5" id="tourPlanProgress">
            <section class="card h-100">
                <div class="card-body">
                    <div class="sp-card-head">
                        <h3 class="card-title mb-0">{{ __('Action Plan Progress') }}</h3>
                        <a href="{{ route('action-plan.index') }}" class="sp-link">{{ __('Open plan') }} {!! $arrow !!}</a>
                    </div>
                    @if ($actionPlanTotal > 0)
                        @php
                            $donePct = round($actionPlanResolved / $actionPlanTotal * 100);
                            $startedPct = round(($actionPlanResolved + $actionPlanInProgress) / $actionPlanTotal * 100);
                            $notStarted = $actionPlanTotal - $actionPlanResolved - $actionPlanInProgress;
                        @endphp
                        <div class="plan-ring-wrap">
                            <div class="plan-ring">
                                <svg viewBox="0 0 36 36" aria-hidden="true">
                                    <circle cx="18" cy="18" r="15.9" fill="none" stroke="var(--sp-surface-2)" stroke-width="3.4"/>
                                    <circle class="js-ring" data-value="{{ $startedPct }}" cx="18" cy="18" r="15.9" fill="none" stroke="var(--sp-managed)" stroke-width="3.4" pathLength="100" stroke-linecap="round" opacity=".3" style="stroke-dasharray: 0 100;"/>
                                    <circle class="js-ring" data-value="{{ $donePct }}" cx="18" cy="18" r="15.9" fill="none" stroke="var(--sp-good)" stroke-width="3.4" pathLength="100" stroke-linecap="round" style="stroke-dasharray: 0 100;"/>
                                </svg>
                                <div class="plan-ring-center"><div><strong class="sp-num">{{ $donePct }}%</strong><span>{{ __('complete') }}</span></div></div>
                            </div>
                            <div class="plan-legend">
                                <div><i style="background: var(--sp-good);"></i>{{ __('Completed') }}<b>{{ $actionPlanResolved }}</b></div>
                                <div><i style="background: var(--sp-managed);"></i>{{ __('In Progress') }}<b>{{ $actionPlanInProgress }}</b></div>
                                <div><i style="background: var(--sp-border-strong);"></i>{{ __('Not Started') }}<b>{{ $notStarted }}</b></div>
                            </div>
                        </div>
                    @else
                        <p class="text-muted small mb-0">{{ __('Complete a self-assessment to generate a personalised action plan from any gaps found.') }}</p>
                    @endif
                </div>
            </section>
        </div>
    </div>

    <x-slot name="scripts">
        <script>
            (function () {
                // Gauge and ring draw in together, once the cards have settled in.
                requestAnimationFrame(() => requestAnimationFrame(() => {
                    const gauge = document.getElementById('scoreGaugeFill');
                    if (gauge) gauge.style.strokeDashoffset = gauge.dataset.offset;
                    document.querySelectorAll('.js-ring').forEach((c) => { c.style.strokeDasharray = c.dataset.value + ' 100'; });
                }));

                @php
                    $tourSteps = [
                        [
                            'title' => __('Welcome to SELFCURE PKS'),
                            'intro' => '<span class="sp-tour-welcome"><img src="'.asset('images/logo-mark.png').'" alt=""></span>'
                                .e(__('This short tour shows you the main features of your dashboard. It takes about a minute, and you can skip it at any time.')),
                        ],
                        [
                            'element' => ['#appSidebar .sidebar-nav', '.app-topbar [data-bs-toggle="offcanvas"]'],
                            'position' => 'right',
                            'title' => __('Your menu'),
                            'intro' => e(__('Move between your Dashboard, Assessment History, Action Plan and the User Guide from here.')),
                        ],
                        [
                            'element' => '#tourStartAssessment',
                            'title' => __('Start an assessment'),
                            'intro' => e(__('Answer 17 simple questions about how your business handles cybersecurity. It takes about 10 minutes, and your answers save automatically so you can stop and continue later.')),
                        ],
                        [
                            'element' => '#tourScoreCard',
                            'title' => __('Your cyber maturity score'),
                            'intro' => e(__('Your overall score out of 100, with your maturity level and risk level. A higher score means your business is better protected.')),
                        ],
                        [
                            'element' => '#tourDomainScores',
                            'title' => __('Your score in 5 areas'),
                            'intro' => e(__('See how you do in Identify, Protect, Detect, Respond and Recover. The thin line marks 65%, the Managed level. Start with your lowest area.')),
                        ],
                        [
                            'element' => '#tourActionPlan',
                            'title' => __('Your next step'),
                            'intro' => e(__('The most urgent action from your assessment, with simple steps to follow. Click "Continue this action" to work on it.')),
                        ],
                        [
                            'element' => '#tourPlanProgress',
                            'title' => __('Track your progress'),
                            'intro' => e(__('See how many actions you have completed. When they are all done, retake the assessment to see your score improve.')),
                        ],
                        [
                            'element' => '#tourLanguage',
                            'title' => __('Switch language'),
                            'intro' => e(__('Change between English and Bahasa Melayu at any time.')),
                        ],
                        [
                            'element' => '#tourNotifications',
                            'title' => __('Notifications'),
                            'intro' => e(__('A red number appears here when the administrator sends you feedback or when you complete your Action Plan.')),
                        ],
                        [
                            'element' => '#spTourBtn',
                            'title' => __("You're all set"),
                            'intro' => e(__('Click this button on any page to see a short tour of that page. Step-by-step help is also in the User Guide.')),
                        ],
                    ];
                @endphp
                // Starts on first login or with ?tour=1; the "?" button replays it.
                sp_registerTour('dashboard', {{ Js::from($tourSteps) }}, { persist: 'server' });
            })();
        </script>
    </x-slot>
</x-app-layout>
