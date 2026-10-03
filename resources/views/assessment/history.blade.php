<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="fs-4 fw-semibold mb-0">{{ __('Assessment History') }}</h2>
                <p class="text-muted mb-0">{{ __('Track how your Cyber Maturity Score has changed over time.') }}</p>
            </div>
            <span class="badge-tracked maturity-Managed d-inline-block">{{ __('Total Complete Audits:') }} {{ $assessments->count() }}</span>
        </div>
    </x-slot>

    @if ($assessments->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body">
                <p class="text-muted mb-2">{{ __("You haven't completed any assessments yet.") }}</p>
                <a href="{{ route('assessment.start') }}" class="btn btn-primary btn-sm">{{ __('Start Your First Assessment') }}</a>
            </div>
        </div>
    @else
        <div class="card shadow-sm mb-4" id="tourTrend">
            <div class="card-body">
                <h5 class="card-title mb-3">{{ __('Score Trend') }}</h5>
                <div style="height: 260px;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        <div class="card shadow-sm" id="tourHistoryList">
            <div class="card-body">
                <h5 class="card-title mb-3">{{ __('Historical Assessments') }}</h5>
                @foreach ($assessments as $assessment)
                    <div class="card shadow-sm mb-2">
                        <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <div class="fw-medium">{{ $assessment->completed_at->format('d M Y, h:i A') }}</div>
                                <div class="text-muted small">{{ $assessment->overall_score }}% &middot; <span class="badge maturity-{{ $assessment->overall_level }}">{{ __($assessment->overall_level) }}</span></div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('assessment.result', $assessment) }}" class="btn btn-outline-primary btn-sm">{{ __('View Result') }}</a>
                                <a href="{{ route('assessment.report', $assessment) }}" class="btn btn-outline-secondary btn-sm">{{ __('PDF') }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <x-slot name="scripts">
        <script>
            @php
                $tourSteps = [
                    [
                        'element' => '#tourTrend',
                        'title' => __('Your score over time'),
                        'intro' => e(__('This chart shows how your score has changed with each assessment. A rising line means your business is getting safer.')),
                    ],
                    [
                        'element' => '#tourHistoryList .card-body .card',
                        'title' => __('Past assessments'),
                        'intro' => e(__('Every completed assessment is kept here. Open the full result or download its PDF report.')),
                    ],
                ];
            @endphp
            sp_registerTour('history', {{ Js::from($tourSteps) }});

            (function () {
                const canvas = document.getElementById('trendChart');
                if (!canvas) return;

                const labels = @json($assessments->sortBy('completed_at')->pluck('completed_at')->map(fn($d) => $d->format('d M')));
                const scores = @json($assessments->sortBy('completed_at')->pluck('overall_score')->values());

                new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: {{ Js::from(__('Overall Score (%)')) }},
                            data: scores,
                            borderColor: '#2148c0',
                            backgroundColor: 'rgba(33, 72, 192, 0.15)',
                            tension: 0.3,
                            fill: true,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: { y: { min: 0, max: 100 } },
                        plugins: { legend: { display: false } },
                    },
                });
            })();
        </script>
    </x-slot>
</x-app-layout>
