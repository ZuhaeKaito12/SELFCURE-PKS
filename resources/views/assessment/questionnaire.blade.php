<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-3">
            <div>
                <span class="badge-tracked">{{ __('Self-assessment') }}</span>
                <h2 class="fw-bold mb-0 mt-2">{{ __('SME Cybersecurity Questionnaire') }}</h2>
                <p class="text-muted mb-0">{{ __('Answer honestly — there are no wrong answers.') }}</p>
            </div>
            <div class="text-end" style="min-width: 200px;" id="tourQProgress">
                <span class="sp-mono small fw-semibold">{{ __('Progress') }} <span id="progressCount">0</span>/{{ $domains->flatMap->questions->count() }}</span>
                <div class="progress answer-progress mt-2" aria-hidden="true">
                    <div id="answerProgressBar" class="progress-bar"></div>
                </div>
                <div id="autosaveHint" class="autosave-hint text-success small mt-1" role="status">{{ __('Saved') }} ✓</div>
            </div>
        </div>
    </x-slot>

    @if (! empty($readOnly))
        <div class="alert alert-info">{{ __('This assessment has already been completed. You are viewing your submitted answers.') }}</div>
    @endif

    <form method="POST" action="{{ route('assessment.submit', $assessment) }}" id="assessmentForm" data-answer-url="{{ route('assessment.answer', $assessment) }}">
        @csrf

        <ul class="nav nav-pills mb-4" id="domainTabs">
            @foreach ($domains as $domain)
                <li class="nav-item">
                    <button class="nav-link {{ $loop->first ? 'active' : '' }}" type="button" data-bs-toggle="pill" data-bs-target="#domain-{{ $domain->code }}">{{ $domain->translatedName() }}</button>
                </li>
            @endforeach
        </ul>

        <div class="tab-content">
            @php $qNumber = 0; $qTotal = $domains->flatMap->questions->count(); @endphp
            @foreach ($domains as $domain)
                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="domain-{{ $domain->code }}">
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="q-domain-head">
                                <span class="domain-icon"><x-domain-icon :code="$domain->code" :size="20" /></span>
                                <div>
                                    <h3 class="fs-5 mb-0">{{ $domain->translatedName() }}</h3>
                                    <p class="text-muted small mb-0">{{ $domain->translatedDescription() }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @foreach ($domain->questions as $question)
                                @php $qNumber++; @endphp
                                <fieldset class="q-item">
                                    <span class="q-num">{{ __('Question :n of :total', ['n' => $qNumber, 'total' => $qTotal]) }}</span>
                                    <legend class="q-text fs-6">{{ $question->translatedText() }}</legend>
                                    <div class="answer-scale">
                                        @foreach ($scaleLabels as $value => $label)
                                            <input type="radio" class="btn-check js-answer" autocomplete="off"
                                                   name="answers[{{ $question->id }}]"
                                                   id="q{{ $question->id }}_{{ $value }}"
                                                   value="{{ $value }}"
                                                   data-domain="{{ $domain->code }}"
                                                   data-question-id="{{ $question->id }}"
                                                   {{ (int) ($existingAnswers[$question->id] ?? 0) === $value ? 'checked' : '' }}
                                                   {{ ! empty($readOnly) ? 'disabled' : 'required' }}>
                                            <label for="q{{ $question->id }}_{{ $value }}">
                                                <span class="answer-n">{{ $value }}</span>
                                                <span class="answer-l">{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <details class="q-tip">
                                        <summary>
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5m0-8h.01"/></svg>
                                            {{ __('Why we ask this') }}
                                        </summary>
                                        <p>{{ $question->translatedGapDescription() }}</p>
                                    </details>
                                </fieldset>
                            @endforeach
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mb-5">
                        @if (! $loop->first)
                            <button type="button" class="btn btn-outline-secondary js-next-domain" data-next="{{ $domains[$loop->index - 1]->code }}">&larr; {{ $domains[$loop->index - 1]->translatedName() }}</button>
                        @else
                            <span></span>
                        @endif
                        @if (! $loop->last)
                            <button type="button" class="btn btn-primary js-next-domain" data-next="{{ $domains[$loop->index + 1]->code }}">{{ __('Next') }}: {{ $domains[$loop->index + 1]->translatedName() }} &rarr;</button>
                        @elseif (empty($readOnly))
                            <button type="submit" class="btn btn-primary btn-lg">{{ __('Submit Assessment') }}</button>
                        @else
                            <a href="{{ route('assessment.result', $assessment) }}" class="btn btn-primary">{{ __('View Result') }}</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </form>

    <x-slot name="scripts">
        <script>
            (function () {
                document.querySelectorAll('.js-next-domain').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const target = document.querySelector('[data-bs-target="#domain-' + btn.dataset.next + '"]');
                        if (target) new bootstrap.Tab(target).show();
                        window.scrollTo({ top: 0, behavior: 'instant' });
                    });
                });

                const radios = document.querySelectorAll('.js-answer');
                const total = {{ $domains->flatMap->questions->count() }};
                const progressEl = document.getElementById('progressCount');
                const answerUrl = document.getElementById('assessmentForm').dataset.answerUrl;
                const csrfToken = document.querySelector('input[name="_token"]').value;
                const progressBar = document.getElementById('answerProgressBar');
                const hint = document.getElementById('autosaveHint');
                let hintTimeout;

                function updateProgress() {
                    const answered = new Set();
                    radios.forEach((r) => { if (r.checked) answered.add(r.dataset.questionId); });
                    progressEl.textContent = answered.size;
                    progressBar.style.transform = 'scaleX(' + (total ? answered.size / total : 0) + ')';
                }

                function autosave(radio) {
                    if (radio.disabled) return;
                    fetch(answerUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            question_id: radio.dataset.questionId,
                            score: radio.value,
                        }),
                    }).then(() => {
                        hint.classList.add('is-visible');
                        clearTimeout(hintTimeout);
                        hintTimeout = setTimeout(() => { hint.classList.remove('is-visible'); }, 1500);
                    }).catch(() => {});
                }

                radios.forEach((radio) => radio.addEventListener('change', () => {
                    updateProgress();
                    autosave(radio);
                }));

                updateProgress();

                @php
                    $tourSteps = [
                        [
                            'element' => '#domainTabs',
                            'title' => __('Five areas, one at a time'),
                            'intro' => e(__('The questions are grouped into 5 areas. Click an area to jump to it.')),
                        ],
                        [
                            'element' => '.tab-pane.active .answer-scale',
                            'title' => __('Choose your answer'),
                            'intro' => e(__('Pick the option that best describes your business today, from 1 (Never) to 5 (Always). There are no wrong answers.')),
                        ],
                        [
                            'element' => '.tab-pane.active .q-tip',
                            'title' => __('Not sure what a question means?'),
                            'intro' => e(__('Open "Why we ask this" for a short explanation.')),
                        ],
                        [
                            'element' => '#tourQProgress',
                            'title' => __('Saved automatically'),
                            'intro' => e(__('Your progress is shown here. Every answer is saved as soon as you pick it, so you can stop and continue later.')),
                        ],
                        [
                            'element' => '.tab-pane.active .btn-primary',
                            'title' => __('Go to the next area'),
                            'intro' => e(__('When you finish an area, move to the next one. In the last area, click "Submit Assessment" to see your result.')),
                        ],
                    ];
                @endphp
                sp_registerTour('questionnaire', {{ Js::from($tourSteps) }});
            })();
        </script>
    </x-slot>
</x-app-layout>
