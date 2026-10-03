<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="badge-tracked maturity-Managed d-inline-block mb-2">{{ __('National Cybersecurity Agency') }}</span>
                <h2 class="fs-4 fw-semibold mb-0">{{ __('Administrator Portal') }}</h2>
                <p class="text-muted mb-0">{{ __("Monitoring registered Malaysian SMEs' cybersecurity readiness.") }}</p>
            </div>
            <div class="d-flex gap-2" id="tourExportReport">
                <a href="{{ route('admin.reports.summary') }}" class="btn btn-outline-secondary">{{ __('Export Summary Report') }}</a>
                <a href="{{ route('admin.clients.create') }}" class="btn btn-primary">+ {{ __('Register New SME') }}</a>
            </div>
        </div>
    </x-slot>

    <div class="row g-3 mb-4" id="tourMetrics">
        <div class="col-md-3 col-6">
            <div class="metric-card">
                <div class="metric-label">{{ __('Total SME') }}</div>
                <div class="metric-value">{{ $totalClients }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="metric-card">
                <div class="metric-label">{{ __('SME Average Score') }}</div>
                <div class="metric-value">{{ $averageScore }}%</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="metric-card">
                <div class="metric-label">{{ __('High Cyber Risk SME') }}</div>
                <div class="metric-value" style="color: var(--sp-critical);">{{ $highRiskCount }}</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="metric-card">
                <div class="metric-label">{{ __('Complete Audits') }}</div>
                <div class="metric-value">{{ $totalAudits }}</div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-5" id="tourRiskDistribution">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <span class="badge-tracked maturity-Initial d-inline-block mb-2">{{ __('Overall Risk Distribution') }}</span>
                    <h5 class="card-title mb-3">{{ __('Registered SMEs by Risk Level') }}</h5>
                    <canvas id="riskDistributionChart" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-7" id="tourDomainPerformance">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <span class="badge-tracked maturity-Managed d-inline-block mb-2">{{ __('Overall Domain Performance') }}</span>
                    <h5 class="card-title mb-3">{{ __('Average Score by Domain, Across All SMEs') }}</h5>
                    <div class="row row-cols-2 row-cols-md-5 g-3">
                        @foreach ($domainAverages as $domain)
                            <div class="col">
                                <div class="pillar-card p-3 text-center h-100">
                                    <div class="fw-semibold small mb-1">{{ $domain['name'] }}</div>
                                    <div class="fs-5 fw-bold">{{ $domain['average'] }}%</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm" id="tourSmeDirectory">
        <div class="card-body">
            <form method="GET" class="row g-2 mb-3 align-items-center">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search SME by business name, owner, or SSM number') }}" value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="industry" class="form-select" onchange="this.form.submit()">
                        <option value="">{{ __('All industries') }}</option>
                        @foreach ($businessTypes as $type)
                            <option value="{{ $type }}" {{ request('industry') === $type ? 'selected' : '' }}>{{ __($type) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">{{ __('Search') }}</button>
                </div>
            </form>

            @if ($clients->isEmpty())
                <p class="text-muted mb-0">{{ __('No SME clients match your search.') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('SME Company / SSM') }}</th>
                                <th>{{ __('Industry & State') }}</th>
                                <th>{{ __('Maturity Score') }}</th>
                                <th>{{ __('Risk Level') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clients as $client)
                                @php $latest = $client->latestCompletedAssessment; @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $client->business_name ?? $client->name }}</div>
                                        <div class="text-muted small">{{ $client->ssm_number ?? __('No SSM number on file') }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $client->business_type ? __($client->business_type) : '—' }}</div>
                                        <div class="text-muted small">{{ $client->state ?? '—' }}</div>
                                    </td>
                                    <td>{{ $latest?->overall_score !== null ? $latest->overall_score.'%' : '—' }}</td>
                                    <td>
                                        @php $risk = $scoring->riskLevelForPercentage($latest?->overall_score !== null ? (float) $latest->overall_score : null); @endphp
                                        <span class="badge {{ $risk === 'High Risk' ? 'maturity-Initial' : ($risk === 'Medium Risk' ? 'maturity-Developing' : ($risk === 'Low Risk' ? 'maturity-Optimized' : 'bg-light text-muted')) }}">{{ __($risk) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $latest ? 'bg-success' : 'bg-secondary' }}">{{ $latest ? __('Audited') : __('Pending') }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-outline-primary btn-sm">{{ __('Audit Record') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $clients->links() }}</div>
            @endif
        </div>
    </div>

    <x-slot name="scripts">
        <script>
            (function () {
                const riskOrder = ['High Risk', 'Medium Risk', 'Low Risk', 'Not Assessed'];
                const riskTranslations = {
                    'High Risk': {{ Js::from(__('High Risk')) }},
                    'Medium Risk': {{ Js::from(__('Medium Risk')) }},
                    'Low Risk': {{ Js::from(__('Low Risk')) }},
                    'Not Assessed': {{ Js::from(__('Not Assessed')) }},
                };
                const riskColors = { 'High Risk': '#be3b33', 'Medium Risk': '#a86f0c', 'Low Risk': '#187a54', 'Not Assessed': '#cbd2dc' };
                const riskCounts = @json($riskDistribution);
                const keys = riskOrder.filter((r) => riskCounts[r]);
                const labels = keys.map((r) => riskTranslations[r]);
                const data = keys.map((r) => riskCounts[r]);

                new Chart(document.getElementById('riskDistributionChart'), {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: data,
                            backgroundColor: keys.map((r) => riskColors[r]),
                            borderWidth: 0,
                        }],
                    },
                    options: {
                        plugins: { legend: { position: 'bottom' } },
                    },
                });

                const tourSteps = [
                    {
                        element: '.app-header-bar',
                        intro: {{ Js::from(__('Welcome to the SELFCURE PKS admin portal! This quick tour shows you around.')) }},
                    },
                    {
                        element: '#tourMetrics',
                        intro: {{ Js::from(__('These cards summarise all registered SMEs: total count, average score, high-risk count and completed audits.')) }},
                    },
                    {
                        element: '#tourRiskDistribution',
                        intro: {{ Js::from(__('This chart shows how many SMEs fall into each risk level, from High Risk to Not Assessed.')) }},
                        position: 'floating',
                    },
                    {
                        element: '#tourDomainPerformance',
                        intro: {{ Js::from(__('Average scores across all SMEs are broken down by the 5 cybersecurity domains here.')) }},
                        position: 'floating',
                    },
                    {
                        element: '#tourSmeDirectory',
                        intro: {{ Js::from(__('Search and browse every registered SME here, and open their Audit Record to review their assessment.')) }},
                        position: 'floating',
                    },
                    {
                        element: '#tourExportReport',
                        intro: {{ Js::from(__('Export a summary report of all SMEs, or register a new SME, from these buttons.')) }},
                    },
                    {
                        element: '#tourNotifications',
                        intro: {{ Js::from(__("You'll be notified here about important updates, such as newly completed assessments.")) }},
                    },
                ];

                // Starts on first login or with ?tour=1; the "?" button replays it.
                sp_registerTour('main', tourSteps, { persist: 'server' });
            })();
        </script>
    </x-slot>
</x-app-layout>
