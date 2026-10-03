<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-2">
            <div>
                <span class="badge-tracked">{{ __('Official Cyber Maturity') }}</span>
                <h2 class="fw-bold mb-0 mt-2">{{ $assessment->user->business_name ?? $assessment->user->name }}</h2>
                <p class="text-muted mb-0">{{ __('Completed on') }} {{ $assessment->completed_at->format('d M Y, h:i A') }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2" id="tourResultActions">
                <a href="{{ route('assessment.start') }}" class="btn btn-outline-secondary">{{ __('Retake Assessment') }}</a>
                <a href="{{ route('action-plan.index') }}" class="btn btn-outline-primary">{{ __('Open Action Roadmap') }}</a>
                <a href="{{ route('assessment.report', $assessment) }}" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="me-1"><path d="M12 3v12m0 0-4-4m4 4 4-4"/><path d="M4 17v3h16v-3"/></svg>{{ __('Download PDF Report') }}
                </a>
            </div>
        </div>
    </x-slot>

    @php
        $orderedScores = $assessment->domainScores->sortBy('domain.order')->values();
        $byScore = $orderedScores->sortByDesc('score')->values();
        $nameList = fn ($items) => $items->map(fn ($d) => $d->domain->translatedName())->join(', ', ' '.__('and').' ');
        $strongest = $byScore->filter(fn ($d) => (float) $d->score >= 65)->take(2);
        // Below the Managed line (65%); if every domain is above it, just the single lowest under 85%.
        $ascending = $byScore->reverse()->values();
        $weakest = $ascending->filter(fn ($d) => (float) $d->score < 65)->take(2)->values();
        if ($weakest->isEmpty()) {
            $weakest = $ascending->filter(fn ($d) => (float) $d->score < 85)->take(1)->values();
        }
        $levelColor = ['Initial' => 'var(--sp-critical)', 'Developing' => 'var(--sp-warn)', 'Managed' => 'var(--sp-managed)', 'Optimized' => 'var(--sp-good)'];

        // Radar chart geometry (inline SVG, no library needed).
        $cx = 170; $cy = 165; $radius = 115; $n = max($orderedScores->count(), 1);
        $point = function ($i, $r) use ($cx, $cy, $n) {
            $a = -M_PI / 2 + $i * 2 * M_PI / $n;
            return [round($cx + $r * cos($a), 2), round($cy + $r * sin($a), 2)];
        };
        $ring = fn ($f) => $orderedScores->keys()->map(fn ($i) => implode(',', $point($i, $radius * $f)))->join(' ');
        $shape = $orderedScores->map(fn ($d, $i) => implode(',', $point($i, $radius * (float) $d->score / 100)))->join(' ');
    @endphp

    <section class="card mb-4" id="tourResultScore">
        <div class="card-body result-hero">
            <div>
                <span class="badge-tracked text-muted">{{ __('Overall Result') }}</span>
                <div class="result-score sp-num">{{ $assessment->overall_score }}<small> / 100</small></div>
                <div class="d-flex flex-wrap gap-2 my-3">
                    <span class="badge sp-dot maturity-{{ $assessment->overall_level }}">{{ __($assessment->overall_level) }}</span>
                    <span class="badge sp-dot {{ $riskLevel === 'High Risk' ? 'maturity-Initial' : ($riskLevel === 'Medium Risk' ? 'maturity-Developing' : 'maturity-Optimized') }}">{{ __($riskLevel) }}</span>
                </div>
                <p class="result-copy mb-0">
                    @if ($strongest->isNotEmpty())
                        {!! __('Your strongest areas are :areas.', ['areas' => '<b>'.e($nameList($strongest)).'</b>']) !!}
                    @endif
                    @if ($weakest->isNotEmpty())
                        {!! __(':areas are where a few small changes will raise your score the most.', ['areas' => '<b>'.e($nameList($weakest)).'</b>']) !!}
                    @endif
                </p>
                <div class="level-key" aria-label="{{ __('Maturity levels') }}">
                    @foreach (['Initial' => '<40', 'Developing' => '40–64', 'Managed' => '65–84', 'Optimized' => '85+'] as $lvl => $range)
                        <div class="{{ $assessment->overall_level === $lvl ? 'is-current' : '' }}" style="--c: {{ $levelColor[$lvl] }};">{{ __($lvl) }}<br>{{ $range }}</div>
                    @endforeach
                </div>
            </div>
            <svg class="result-radar" viewBox="-30 0 400 320" role="img"
                 aria-label="{{ __('Cyber Maturity by Domain') }}: {{ $orderedScores->map(fn ($d) => $d->domain->translatedName().' '.$d->score.'%')->join(', ') }}">
                @foreach ([0.25, 0.5, 0.75, 1] as $f)
                    <polygon points="{{ $ring($f) }}" fill="{{ $f === 1 ? 'var(--sp-surface-2)' : 'none' }}" stroke="var(--sp-border)"/>
                @endforeach
                <polygon points="{{ $ring(0.65) }}" fill="none" stroke="var(--sp-border-strong)" stroke-dasharray="3 3"/>
                @foreach ($orderedScores as $i => $d)
                    @php [$x, $y] = $point($i, $radius); @endphp
                    <line x1="{{ $cx }}" y1="{{ $cy }}" x2="{{ $x }}" y2="{{ $y }}" stroke="var(--sp-border)"/>
                @endforeach
                <polygon points="{{ $shape }}" fill="rgba(33, 72, 192, .12)" stroke="var(--sp-blue)" stroke-width="2" stroke-linejoin="round"/>
                @foreach ($orderedScores as $i => $d)
                    @php
                        [$px, $py] = $point($i, $radius * (float) $d->score / 100);
                        [$lx, $ly] = $point($i, $radius + 26);
                        $anchor = abs($lx - $cx) < 5 ? 'middle' : ($lx > $cx ? 'start' : 'end');
                    @endphp
                    <circle cx="{{ $px }}" cy="{{ $py }}" r="4" fill="var(--sp-surface)" stroke="{{ $levelColor[$d->level] ?? 'var(--sp-blue)' }}" stroke-width="2.5"/>
                    <text x="{{ $lx }}" y="{{ $ly }}" text-anchor="{{ $anchor }}">{{ $d->domain->translatedName() }}</text>
                    <text class="val" x="{{ $lx }}" y="{{ $ly + 15 }}" text-anchor="{{ $anchor }}">{{ number_format((float) $d->score, 1) }}%</text>
                @endforeach
            </svg>
        </div>
    </section>

    @php $isAi = ($insights['source'] ?? 'rules') === 'ai'; @endphp
    <section class="card ai-assistant mb-4" id="aiAssistant" data-explain-url="{{ route('assessment.explain', $assessment) }}">
        <button type="button" class="ai-bot js-bot" data-state="idle" aria-label="{{ __('Explain My Results') }}" title="{{ __('Explain My Results') }}">
            <svg viewBox="0 0 64 64" width="64" height="64" aria-hidden="true">
                <line x1="32" y1="7" x2="32" y2="15" class="bot-stroke"></line>
                <circle cx="32" cy="6" r="3.2" class="bot-antenna"></circle>
                <rect x="5" y="27" width="5" height="12" rx="2.5" class="bot-ear"></rect>
                <rect x="54" y="27" width="5" height="12" rx="2.5" class="bot-ear"></rect>
                <rect x="10" y="15" width="44" height="37" rx="13" class="bot-head"></rect>
                <rect x="16" y="22" width="32" height="18" rx="9" class="bot-visor"></rect>
                <g class="bot-eyes">
                    <rect x="23" y="28" width="5" height="6" rx="2.5"></rect>
                    <rect x="36" y="28" width="5" height="6" rx="2.5"></rect>
                </g>
                <g class="bot-mouth">
                    <rect x="27" y="44" width="2.4" height="3" rx="1.2"></rect>
                    <rect x="30.8" y="44" width="2.4" height="3" rx="1.2"></rect>
                    <rect x="34.6" y="44" width="2.4" height="3" rx="1.2"></rect>
                </g>
            </svg>
        </button>

        <div class="card-body">
            <div class="ai-assistant-head">
                <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                    <span class="ai-assistant-name">{{ $isAi ? __('AI Cybersecurity Assistant') : __('Cybersecurity Assistant') }}</span>
                    <span class="badge-tracked">{{ $isAi ? __('AI-generated') : __('Based on your answers') }}</span>
                </div>
                <div class="ai-bubble js-bubble" aria-live="polite">{{ __("Here's what I found from your assessment.") }}</div>
            </div>

            <div class="ai-section">
                <span class="ai-label">{{ __('Overall Security Status') }}</span>
                <p class="ai-overall js-typed mb-0">{{ $insights['overall'] }}</p>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6 js-reveal">
                    <div class="ai-panel ai-panel-good h-100">
                        <span class="ai-label">✓ {{ __("What You're Doing Well") }}</span>
                        @if (! empty($insights['strengths']))
                            <ul class="ai-list mb-0">
                                @foreach ($insights['strengths'] as $strength)
                                    <li>{{ $strength }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="small text-muted mb-0">{{ __('No practices are fully in place yet. Every action you complete builds a stronger foundation.') }}</p>
                        @endif
                    </div>
                </div>
                <div class="col-md-6 js-reveal">
                    <div class="ai-panel ai-panel-warn h-100">
                        <span class="ai-label">⚠ {{ __('Areas That Need Attention') }}</span>
                        @if (! empty($insights['improvements']))
                            <ul class="ai-list mb-0">
                                @foreach ($insights['improvements'] as $improvement)
                                    <li>{{ $improvement }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="small text-muted mb-0">{{ __('No significant gaps were found in your current practices.') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="ai-next js-reveal">
                <span class="ai-label">→ {{ __('Recommended Next Step') }}</span>
                <p class="mb-0">{{ $insights['next_step'] }}</p>
            </div>

            @if ($recommendations->isNotEmpty())
                <div class="ai-section js-reveal">
                    <div class="d-flex justify-content-between align-items-baseline flex-wrap gap-2 mb-2">
                        <span class="ai-label mb-0">{{ __('Priority Actions') }}</span>
                        <a href="{{ route('action-plan.index') }}" class="small fw-semibold">{{ __('Open Action Roadmap') }} &rarr;</a>
                    </div>
                    <div class="d-grid gap-2">
                        @foreach ($recommendations->take(3) as $rec)
                            @php $planItem = $planItems->get($rec['action_id']); @endphp
                            <a href="{{ route('action-plan.index') }}{{ $planItem ? '#action-'.$planItem->id : '' }}" class="ai-action-link">
                                <x-domain-icon :code="$rec['domain_code']" :size="18" class="text-primary flex-shrink-0" />
                                <span class="flex-grow-1">{{ $rec['action_title'] }}</span>
                                <span class="badge priority-chip priority-{{ $rec['priority'] }}">{{ $rec['priority'] === 'high' ? __('High') : __('Medium') }}</span>
                                @if ($planItem)
                                    <x-action-status :status="$planItem->status" />
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="ai-explain js-reveal">
                <button type="button" class="btn btn-outline-primary btn-sm js-explain">{{ __('Explain My Results') }}</button>
                <div class="ai-explain-panel js-explain-panel" hidden>
                    <span class="ai-thinking js-thinking" aria-hidden="true"><i></i><i></i><i></i></span>
                    <p class="mb-0 js-explain-text"></p>
                </div>
            </div>

            @if ($isAi)
                <p class="text-muted small mt-3 mb-0">{{ __('AI-generated from your answers. Please use it as general guidance, not professional or legal advice.') }}</p>
            @endif
        </div>
    </section>

    @if ($assessment->auditor_notes)
        <div class="card admin-note mb-4">
            <div class="card-body">
                <span class="badge-tracked maturity-Developing d-block mb-2">{{ __('Feedback from Administrator') }}</span>
                <p class="mb-0" style="white-space: pre-line;">{{ $assessment->auditor_notes }}</p>
            </div>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <div class="sp-card-head">
                <h3 class="card-title mb-0">{{ __('Score by Domain') }}</h3>
                <span class="domain-legend d-inline-flex"><i aria-hidden="true"></i>{{ __('Managed level · 65%') }}</span>
            </div>
            @foreach ($orderedScores as $domainScore)
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
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-1">
                <h5 class="card-title mb-0">{{ __('Findings & Recommended Actions') }}</h5>
                @if ($recommendations->isNotEmpty())
                    <a href="{{ route('action-plan.index') }}" class="btn btn-outline-primary btn-sm">{{ __('Track in Action Roadmap') }}</a>
                @endif
            </div>
            @if ($recommendations->isEmpty())
                <p class="text-success mb-0 mt-2">{{ __('No significant gaps were identified. Keep up the good practices and review your cybersecurity posture periodically.') }}</p>
            @else
                <p class="text-muted small">{{ __('Each gap found in your assessment, with the action that addresses it. Most urgent first.') }}</p>
                <div class="accordion" id="recommendationsAccordion">
                    @foreach ($recommendations as $index => $rec)
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#rec{{ $index }}">
                                    <span class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge priority-chip priority-{{ $rec['priority'] }}">{{ $rec['priority'] === 'high' ? __('High') : __('Medium') }}</span>
                                        <span>{{ $rec['action_title'] }}</span>
                                        <span class="domain-pill">{{ $rec['domain_name'] }}</span>
                                    </span>
                                </button>
                            </h2>
                            <div id="rec{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#recommendationsAccordion">
                                <div class="accordion-body">
                                    <p><strong>{{ __('What we found:') }}</strong> {{ $rec['finding'] }}</p>
                                    <p><strong>{{ __('Why this matters:') }}</strong> {{ $rec['gap_description'] }}</p>
                                    @if (! empty($rec['steps']))
                                        <p class="mb-1"><strong>{{ __('Recommended steps:') }}</strong></p>
                                        <ol class="small mb-3">
                                            @foreach ($rec['steps'] as $step)
                                                <li>{{ $step }}</li>
                                            @endforeach
                                        </ol>
                                    @endif
                                    <p class="text-muted small mb-1"><strong>{{ __('Based on your assessment:') }}</strong> &ldquo;{{ $rec['question_text'] }}&rdquo;</p>
                                    <p class="text-muted small mb-0"><strong>{{ __('Related regulation:') }}</strong> {{ $rec['regulation_reference'] }}</p>
                                    @php $resources = config('resource_library.'.$rec['domain_code'], []); @endphp
                                    @if (! empty($resources))
                                        <p class="text-muted small mb-0 mt-2">
                                            <strong>{{ __('Helpful resources:') }}</strong>
                                            @foreach ($resources as $resource)
                                                <a href="{{ $resource['url'] }}" target="_blank" rel="noopener">{{ $resource['title'] }}</a>{{ ! $loop->last ? ' · ' : '' }}
                                            @endforeach
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <div class="mb-4">
        <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#technicalDetails">
            {{ __('Show Detailed Technical Breakdown') }}
        </button>
        <div class="collapse mt-3" id="technicalDetails">
            <div class="row g-4 mb-4">
                <div class="col-lg-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <span class="badge-tracked maturity-Managed d-inline-block mb-2">{{ __('Result Graph') }}</span>
                            <h5 class="card-title mb-3">{{ __('Cyber Maturity by Domain') }}</h5>
                            <canvas id="radarChart" height="260"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <span class="badge-tracked maturity-Developing d-inline-block mb-2">{{ __('Result Chart') }}</span>
                            <h5 class="card-title mb-3">{{ __('Gap Analysis') }}</h5>
                            <canvas id="gapChart" height="260"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Detailed Domain Gap Breakdown') }}</h5>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>{{ __('Domain Code') }}</th>
                                    <th>{{ __('NIST Domain Name') }}</th>
                                    <th>{{ __('Score (%)') }}</th>
                                    <th>{{ __('Target Gap') }}</th>
                                    <th>{{ __('Evaluation Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($assessment->domainScores->sortBy('domain.order') as $domainScore)
                                    <tr>
                                        <td class="text-muted">{{ strtoupper(substr($domainScore->domain->code, 0, 2)) }}</td>
                                        <td>{{ $domainScore->domain->translatedName() }}</td>
                                        <td>{{ $domainScore->score }}%</td>
                                        <td>{{ round(100 - $domainScore->score, 2) }} {{ __('pts') }}</td>
                                        <td><span class="badge {{ $domainScore->score >= 65 ? 'maturity-Optimized' : 'maturity-Developing' }}">{{ $domainScore->score >= 65 ? __('Satisfying') : __('Needs Improvement') }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('dashboard') }}" class="btn btn-link">&larr; {{ __('Back to Dashboard') }}</a>

    <x-slot name="scripts">
        <script>
            (function () {
                const collapseEl = document.getElementById('technicalDetails');
                let chartsBuilt = false;

                collapseEl.addEventListener('shown.bs.collapse', function () {
                    if (chartsBuilt) return;
                    chartsBuilt = true;

                    const labels = @json($assessment->domainScores->sortBy('domain.order')->map(fn($d) => $d->domain->translatedName())->values());
                    const scores = @json($assessment->domainScores->sortBy('domain.order')->pluck('score')->values());
                    const gaps = scores.map((s) => Math.round((100 - parseFloat(s)) * 100) / 100);

                    new Chart(document.getElementById('radarChart'), {
                        type: 'radar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: {{ Js::from(__('Maturity Score (%)')) }},
                                data: scores,
                                backgroundColor: 'rgba(33, 72, 192, 0.2)',
                                borderColor: '#2148c0',
                                pointBackgroundColor: '#2148c0',
                            }],
                        },
                        options: {
                            scales: { r: { min: 0, max: 100, ticks: { stepSize: 20 } } },
                            plugins: { legend: { display: false } },
                        },
                    });

                    new Chart(document.getElementById('gapChart'), {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: {{ Js::from(__('Gap (percentage points to full maturity)')) }},
                                data: gaps,
                                backgroundColor: '#a86f0c',
                            }],
                        },
                        options: {
                            indexAxis: 'y',
                            scales: { x: { min: 0, max: 100 } },
                            plugins: { legend: { display: false } },
                        },
                    });
                });
            })();
        </script>
        <script>
            (function () {
                const root = document.getElementById('aiAssistant');
                if (!root) return;

                const bot = root.querySelector('.js-bot');
                const bubble = root.querySelector('.js-bubble');
                const typed = root.querySelector('.js-typed');
                const reveals = root.querySelectorAll('.js-reveal');
                const explainBtn = root.querySelector('.js-explain');
                const panel = root.querySelector('.js-explain-panel');
                const thinking = root.querySelector('.js-thinking');
                const explainText = root.querySelector('.js-explain-text');
                const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                const text = {
                    thinking: {{ Js::from(__('Let me look at your answers…')) }},
                    explaining: {{ Js::from(__("Here's your result in simple terms.")) }},
                    done: {{ Js::from(__("Let's look at the areas you can improve.")) }},
                    error: {{ Js::from(__("Sorry, I couldn't load the explanation right now. Please try again.")) }},
                };

                const setState = (state) => { bot.dataset.state = state; };

                // Reveal text word by word while the assistant "speaks".
                function speak(el, content) {
                    if (reduce) {
                        el.textContent = content;
                        return Promise.resolve();
                    }
                    el.textContent = '';
                    const words = content.split(/(\s+)/);
                    const spans = words.map((w) => {
                        const span = document.createElement('span');
                        span.textContent = w;
                        if (w.trim()) span.className = 'ai-word';
                        el.appendChild(span);
                        return span;
                    }).filter((s) => s.className);
                    el.setAttribute('aria-label', content);

                    setState('speaking');
                    return new Promise((resolve) => {
                        let i = 0;
                        const step = Math.max(14, Math.min(32, 1600 / spans.length));
                        const timer = setInterval(() => {
                            if (i >= spans.length) {
                                clearInterval(timer);
                                el.removeAttribute('aria-label');
                                setState('idle');
                                resolve();
                                return;
                            }
                            spans[i++].classList.add('is-shown');
                        }, step);
                    });
                }

                root.classList.add('is-animating');
                speak(typed, typed.textContent.trim()).then(() => {
                    reveals.forEach((el, index) => {
                        setTimeout(() => el.classList.add('is-visible'), reduce ? 0 : index * 90);
                    });
                });

                let loaded = false;
                function explain() {
                    panel.hidden = false;
                    if (loaded) {
                        panel.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'nearest' });
                        return;
                    }
                    loaded = true;
                    explainBtn.disabled = true;
                    thinking.hidden = false;
                    explainText.textContent = '';
                    setState('thinking');
                    bubble.textContent = text.thinking;

                    fetch(root.dataset.explainUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': {{ Js::from(csrf_token()) }},
                        },
                    })
                        .then((r) => { if (!r.ok) throw new Error(); return r.json(); })
                        .then((data) => {
                            thinking.hidden = true;
                            bubble.textContent = text.explaining;
                            return speak(explainText, data.text);
                        })
                        .then(() => { bubble.textContent = text.done; })
                        .catch(() => {
                            loaded = false;
                            explainBtn.disabled = false;
                            thinking.hidden = true;
                            setState('idle');
                            explainText.textContent = text.error;
                        });
                }

                explainBtn.addEventListener('click', explain);
                bot.addEventListener('click', explain);

                @php
                    $tourSteps = [
                        [
                            'element' => '#tourResultScore',
                            'title' => __('Your result'),
                            'intro' => e(__('Your overall score, maturity level and risk level. The chart shows your score in each of the 5 areas.')),
                        ],
                        [
                            'element' => '#tourResultActions',
                            'title' => __('Report and next steps'),
                            'intro' => e(__('Download a PDF report for your records, open your Action Plan, or retake the assessment.')),
                        ],
                        [
                            'element' => '#aiAssistant .ai-assistant-head',
                            'title' => __('Your cybersecurity assistant'),
                            'intro' => e(__('A plain-language summary of your result: what you are doing well, what needs attention, and what to do next.')),
                        ],
                        [
                            'element' => '.js-explain',
                            'title' => __('Want it even simpler?'),
                            'intro' => e(__('Click "Explain My Results", or the robot, for a simpler explanation of your score.')),
                        ],
                        [
                            'element' => '#recommendationsAccordion',
                            'title' => __('Findings and actions'),
                            'intro' => e(__('Each gap that was found, why it matters, and the steps to fix it. Click a finding to open it.')),
                        ],
                    ];
                @endphp
                sp_registerTour('result', {{ Js::from($tourSteps) }});
            })();
        </script>
    </x-slot>
</x-app-layout>
