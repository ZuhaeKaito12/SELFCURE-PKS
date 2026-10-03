<x-app-layout>
    <x-slot name="header">
        <span class="badge-tracked maturity-Managed d-inline-block mb-2">{{ __('NACSA Administrator View') }}</span>
        <h2 class="fs-4 fw-semibold mb-0">{{ __('Assessment Result') }} — {{ $assessment->user->business_name ?? $assessment->user->name }}</h2>
        <p class="text-muted mb-0">@if ($assessment->isCompleted()){{ __('Completed on') }} {{ $assessment->completed_at->format('d M Y, h:i A') }}@else{{ __('In progress') }} &middot; {{ __('Started') }} {{ $assessment->created_at->format('d M Y, h:i A') }}@endif</p>
    </x-slot>

    @if ($assessment->isCompleted())
    <div class="card shadow-sm mb-4">
        <div class="card-body text-center py-4">
            <span class="badge-tracked maturity-Managed d-inline-block mb-2">{{ __('Overall Cyber Maturity Score') }}</span>
            <div class="d-flex justify-content-center my-2">
                <div class="maturity-seal">
                    <span class="seal-score">{{ $assessment->overall_score }}</span>
                    <span class="seal-max">/ 100</span>
                    <span class="seal-label">{{ __($assessment->overall_level) }}</span>
                </div>
            </div>
            <div class="d-flex justify-content-center gap-2 mt-2">
                @if ($riskLevel)
                    <span class="badge {{ $riskLevel === 'High Risk' ? 'maturity-Initial' : ($riskLevel === 'Medium Risk' ? 'maturity-Developing' : 'maturity-Optimized') }} fs-6 px-3 py-2">{{ __($riskLevel) }}</span>
                @endif
            </div>
            <div class="mt-3">
                <a href="{{ route('admin.assessments.report', $assessment) }}" class="btn btn-outline-primary btn-sm">{{ __('Download PDF Report') }}</a>
            </div>
        </div>
    </div>

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

    <div class="card shadow-sm mb-4">
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

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">{{ __('Findings & Recommended Actions') }}</h5>
            @if ($recommendations->isEmpty())
                <p class="text-success mb-0">{{ __('No significant gaps were identified for this client.') }}</p>
            @else
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
                                    <p class="text-muted small mb-1"><strong>{{ __('Based on the assessment:') }}</strong> &ldquo;{{ $rec['question_text'] }}&rdquo; ({{ __('answer') }}: {{ $rec['answer_score'] }}/5)</p>
                                    <p class="text-muted small mb-0"><strong>{{ __('Related regulation:') }}</strong> {{ $rec['regulation_reference'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
    @else
        <div class="card mb-4">
            <div class="card-body empty-state">
                <h3>{{ __('This assessment is still in progress') }}</h3>
                <p class="mb-0">{{ __('Scores, charts and the PDF report appear once the SME owner submits all 17 answers.') }}</p>
            </div>
        </div>
    @endif


    <a href="{{ route('admin.clients.show', $assessment->user) }}" class="btn btn-link">&larr; {{ __('Back to Client') }}</a>

    <x-slot name="scripts">
        @if ($assessment->isCompleted())
        <script>
            (function () {
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
                            backgroundColor: 'rgba(14, 116, 144, 0.2)',
                            borderColor: '#0e7490',
                            pointBackgroundColor: '#0e7490',
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
            })();
        </script>
    @endif
    </x-slot>
</x-app-layout>
