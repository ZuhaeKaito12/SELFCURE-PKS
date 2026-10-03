@php
    $reportNo = 'NS-'.$generatedAt->format('Ymd-Hi');
    $assessed = $totalClients - (int) $riskDistribution->get('Not Assessed', 0);
    $riskOrder = [
        'High Risk' => ['#9f2f28', '#fbeceb'],
        'Medium Risk' => ['#8a5a08', '#fbf3e2'],
        'Low Risk' => ['#14684a', '#e4f3ec'],
        'Not Assessed' => ['#64748b', '#eef1f5'],
    ];
    $sortedDomains = collect($domainAverages)->sortBy('average')->values();
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('National summary of SME cybersecurity readiness') }}</title>
    <style>
        @page { margin: 22mm 18mm 20mm 18mm; }
        * { box-sizing: border-box; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10pt; line-height: 1.5; color: #1f2937; }
        p { margin: 0 0 6pt; }
        h1, h2 { color: #0b1730; margin: 0; font-weight: bold; }
        table { border-collapse: collapse; width: 100%; }
        td, th { vertical-align: top; }

        .page-footer { position: fixed; bottom: -12mm; left: 0; right: 0; height: 8mm; font-size: 7.5pt; line-height: 1.2; color: #8590a2; border-top: 0.5pt solid #d6dbe3; padding-top: 2mm; }

        .masthead td { vertical-align: middle; }
        .brand { font-size: 11pt; font-weight: bold; color: #0b1730; }
        .brand-sub { font-size: 8.5pt; color: #64748b; }
        .doc-meta { text-align: right; font-size: 8.5pt; color: #64748b; line-height: 1.6; }
        .doc-meta b { color: #1f2937; }
        .rule { border-top: 1.5pt solid #0b1730; margin: 8pt 0 18pt; }
        .eyebrow { font-size: 8pt; color: #64748b; text-transform: uppercase; letter-spacing: 0.8pt; margin-bottom: 3pt; }
        .title { font-size: 19pt; line-height: 1.2; margin-bottom: 4pt; }
        .prepared td { font-size: 9pt; padding: 1.5pt 0; }
        .prepared .k { color: #64748b; width: 34mm; }

        .section { margin-top: 20pt; }
        .section-head { border-bottom: 0.75pt solid #cfd6df; padding-bottom: 4pt; margin-bottom: 10pt; }
        .section-head .n { color: #0e7490; font-weight: bold; margin-right: 6pt; }
        .section-head h2 { display: inline; font-size: 12.5pt; }
        .muted { color: #64748b; }
        .small { font-size: 8.5pt; }

        .figures td { width: 33.3%; padding: 10pt 12pt; background: #f1f6f8; border-right: 3pt solid #fff; }
        .figures td:last-child { border-right: 0; }
        .figures b { display: block; font-size: 20pt; color: #0b1730; line-height: 1.1; }
        .figures span { font-size: 8.5pt; color: #64748b; }

        .data th { font-size: 8pt; color: #64748b; font-weight: normal; text-transform: uppercase; letter-spacing: 0.5pt; text-align: left; padding: 0 6pt 5pt 0; border-bottom: 0.75pt solid #cfd6df; }
        .data td { padding: 6pt 6pt 6pt 0; border-bottom: 0.5pt solid #e5e9ef; font-size: 9pt; }
        .data tr { page-break-inside: avoid; }
        .num { text-align: right; white-space: nowrap; }
        .bar { height: 5pt; background: #e8ecf2; margin-top: 4pt; }
        .bar div { height: 5pt; }
        .tag { font-size: 8pt; font-weight: bold; padding: 1.5pt 5pt; }
        .about { margin-top: 22pt; font-size: 8pt; color: #64748b; }
    </style>
</head>
<body>
    <div class="page-footer">
        SELFCURE PKS &middot; {{ __('National summary of SME cybersecurity readiness') }} &middot; {{ $reportNo }}
    </div>

    <table class="masthead">
        <tr>
            <td style="width: 13mm;"><img src="{{ public_path('images/logo-mark-print.jpg') }}" alt="" style="width: 11mm; height: 11mm;"></td>
            <td>
                <div class="brand">SELFCURE PKS</div>
                <div class="brand-sub">{{ __('Administrator report') }}</div>
            </td>
            <td class="doc-meta">
                {{ __('Report no.') }} <b>{{ $reportNo }}</b><br>
                {{ __('Issued') }} <b>{{ $generatedAt->format('d M Y, h:i A') }}</b>
            </td>
        </tr>
    </table>
    <div class="rule"></div>

    <div class="eyebrow">{{ __('Administrator report') }}</div>
    <h1 class="title">{{ __('National summary of SME cybersecurity readiness') }}</h1>
    <table class="prepared" style="margin-top: 6pt;">
        <tr><td class="k">{{ __('Data as at') }}</td><td>{{ $generatedAt->format('d M Y, h:i A') }}</td></tr>
        <tr><td class="k">{{ __('Coverage') }}</td><td>{{ __(':clients registered SMEs, :audits completed assessments', ['clients' => $totalClients, 'audits' => $totalAudits]) }}</td></tr>
        <tr><td class="k">{{ __('Intended for') }}</td><td>{{ __('Internal monitoring by the system administrator') }}</td></tr>
    </table>

    {{-- 1. Overview --}}
    <div class="section">
        <div class="section-head"><span class="n">1</span><h2>{{ __('Overview') }}</h2></div>
        <table class="figures">
            <tr>
                <td><b>{{ $totalClients }}</b><span>{{ __('Registered SMEs') }}</span></td>
                <td><b>{{ $averageScore }}%</b><span>{{ __('Average score, all completed assessments') }}</span></td>
                <td><b>{{ $totalAudits }}</b><span>{{ __('Completed assessments') }}</span></td>
            </tr>
        </table>
        <p style="margin-top: 10pt;">
            {{ __(':assessed of the :total registered SMEs have completed at least one assessment.', ['assessed' => $assessed, 'total' => $totalClients]) }}
            @if ($riskDistribution->get('High Risk'))
                {{ trans_choice('{1} One is currently rated high risk and should be followed up first.|[2,*] :count are currently rated high risk and should be followed up first.', $riskDistribution->get('High Risk'), ['count' => $riskDistribution->get('High Risk')]) }}
            @endif
            @if ($sortedDomains->isNotEmpty())
                {{ __('Across all assessments, the weakest area is :area (average :score%).', ['area' => $sortedDomains->first()['name'], 'score' => $sortedDomains->first()['average']]) }}
            @endif
        </p>
    </div>

    {{-- 2. Risk distribution --}}
    <div class="section" style="page-break-inside: avoid;">
        <div class="section-head"><span class="n">2</span><h2>{{ __('Risk distribution') }}</h2></div>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 34%;">{{ __('Risk level') }}</th>
                    <th>{{ __('Share of SMEs') }}</th>
                    <th style="width: 14%;" class="num">{{ __('SMEs') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($riskOrder as $level => [$ink, $tint])
                    @php $count = (int) $riskDistribution->get($level, 0); $share = $totalClients ? round($count / $totalClients * 100) : 0; @endphp
                    <tr>
                        <td><span class="tag" style="color: {{ $ink }}; background: {{ $tint }};">{{ __($level) }}</span></td>
                        <td style="padding-top: 8pt;"><div class="bar"><div style="width: {{ $share }}%; background: {{ $ink }};"></div></div></td>
                        <td class="num">{{ $count }} <span class="muted">({{ $share }}%)</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p class="small muted" style="margin-top: 6pt;">{{ __("Risk level is based on each SME's most recent completed assessment.") }}</p>
    </div>

    {{-- 3. Average score by area --}}
    <div class="section" style="page-break-inside: avoid;">
        <div class="section-head"><span class="n">3</span><h2>{{ __('Average score by area') }}</h2></div>
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 34%;">{{ __('Area') }}</th>
                    <th>{{ __('Average score') }}</th>
                    <th style="width: 14%;" class="num">%</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($domainAverages as $domain)
                    @php $avg = (float) $domain['average']; $colour = $avg >= 85 ? '#14684a' : ($avg >= 65 ? '#1d3fa8' : ($avg >= 40 ? '#8a5a08' : '#9f2f28')); @endphp
                    <tr>
                        <td><b style="color: #0b1730;">{{ $domain['name'] }}</b></td>
                        <td style="padding-top: 8pt;"><div class="bar"><div style="width: {{ max(0, min(100, $avg)) }}%; background: {{ $colour }};"></div></div></td>
                        <td class="num"><b>{{ number_format($avg, 1) }}</b></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p class="small muted" style="margin-top: 6pt;">{{ __('Averages include every completed assessment, not only the latest one for each SME.') }}</p>
    </div>

    {{-- 4. Registered SMEs --}}
    <div class="section">
        <div class="section-head"><span class="n">4</span><h2>{{ __('Registered SMEs') }}</h2></div>
        <table class="data">
            <thead>
                <tr>
                    <th>{{ __('Business') }}</th>
                    <th style="width: 22%;">{{ __('Industry') }}</th>
                    <th style="width: 14%;">{{ __('State') }}</th>
                    <th style="width: 10%;" class="num">{{ __('Score') }}</th>
                    <th style="width: 14%; padding-left: 8pt;">{{ __('Level') }}</th>
                    <th style="width: 16%;">{{ __('Risk') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($clients as $client)
                    @php [$ink, $tint] = $riskOrder[$client['risk']] ?? ['#64748b', '#eef1f5']; @endphp
                    <tr>
                        <td><b style="color: #0b1730;">{{ $client['business_name'] }}</b></td>
                        <td>{{ $client['business_type'] ? __($client['business_type']) : '—' }}</td>
                        <td>{{ $client['state'] ?? '—' }}</td>
                        <td class="num">{{ $client['score'] !== null ? number_format((float) $client['score'], 1) : '—' }}</td>
                        <td style="padding-left: 8pt;">{{ $client['level'] ? __($client['level']) : '—' }}</td>
                        <td><span class="tag" style="color: {{ $ink }}; background: {{ $tint }};">{{ __($client['risk']) }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="about">
        {{ __('About this report: the figures come from self-assessments completed by each SME on SELFCURE PKS, not from technical audits. Use it to decide which businesses to follow up with. It does not replace the individual audit record for each SME.') }}
    </p>
</body>
</html>
