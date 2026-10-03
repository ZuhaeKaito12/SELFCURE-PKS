@php
    $user = $assessment->user;
    $business = $user->business_name ?: $user->name;
    $domainScores = $assessment->domainScores->sortBy('domain.order')->values();
    $byScore = $domainScores->sortByDesc('score')->values();
    $strongest = $byScore->first();
    $weakest = $byScore->last();
    $highCount = $recommendations->where('priority', 'high')->count();
    $reportNo = 'SCP-'.str_pad((string) $assessment->id, 4, '0', STR_PAD_LEFT);

    $levelMeaning = [
        'Initial' => __('Basic protections are mostly missing, so the business is exposed to common attacks such as phishing and account takeover.'),
        'Developing' => __('Some protections are in place, but there are clear gaps that common attacks could take advantage of.'),
        'Managed' => __('Most everyday protections are in place. The remaining gaps are specific and can be fixed one at a time.'),
        'Optimized' => __('Protections are strong and well documented. The focus now is on keeping them up to date.'),
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('Cybersecurity Self-Assessment Report') }} — {{ $business }}</title>
    <style>
        @page { margin: 18mm 20mm 20mm 20mm; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10pt; line-height: 1.5; color: #1f2937; }
        p { margin: 0 0 7pt; }
        h1, h2 { color: #0b1730; margin: 0; font-weight: bold; }
        ul, ol { margin: 0 0 6pt; padding-left: 14pt; }
        li { margin-bottom: 2pt; }
        .muted { color: #64748b; }
        .small { font-size: 8.5pt; }

        /* Running footer text; the page number is drawn by the controller. */
        .page-footer { position: fixed; bottom: -12mm; left: 0; right: 0; height: 8mm; font-size: 7.5pt; line-height: 1.2; color: #8590a2; border-top: 0.5pt solid #d6dbe3; padding-top: 2mm; }

        /* Masthead */
        .masthead { border-bottom: 1.5pt solid #0b1730; padding-bottom: 6pt; margin-bottom: 13pt; }
        .masthead-meta { float: right; text-align: right; font-size: 8.5pt; color: #64748b; line-height: 1.6; }
        .masthead-meta b { color: #1f2937; }
        .masthead img { width: 10mm; height: 10mm; vertical-align: middle; margin-right: 6pt; }
        .brand { font-size: 11pt; font-weight: bold; color: #0b1730; vertical-align: middle; }

        .eyebrow { font-size: 8pt; color: #64748b; text-transform: uppercase; letter-spacing: 0.8pt; margin-bottom: 3pt; }
        .title { font-size: 21pt; line-height: 1.15; margin-bottom: 6pt; }
        .details { font-size: 9pt; color: #475467; margin: 0; }
        .details b { color: #1f2937; font-weight: normal; }

        /* Sections: a numbered heading and plain text, no boxes. */
        .section { margin-top: 12pt; }
        h2 { font-size: 12.5pt; border-bottom: 0.75pt solid #cfd6df; padding-bottom: 3pt; margin-bottom: 8pt; }
        h2 .n { color: #2148c0; margin-right: 6pt; }

        .key-facts { margin: 0 0 9pt; }
        .key-facts p { margin: 0 0 2pt; font-size: 10.5pt; }
        .key-facts .k { color: #64748b; }
        .key-facts .v { font-weight: bold; color: #0b1730; }

        .area { margin-bottom: 5pt; page-break-inside: avoid; }
        .area-name { font-weight: bold; color: #0b1730; }
        .area-desc { font-size: 8.5pt; color: #64748b; margin: 1pt 0 0; }

        .about { margin-top: 10pt; font-size: 7.5pt; line-height: 1.4; color: #64748b; }
    </style>
</head>
<body>
    <div class="page-footer">
        SELFCURE PKS &middot; {{ __('Cybersecurity Self-Assessment Report') }} &middot; {{ $business }} &middot; {{ $reportNo }}
    </div>

    <div class="masthead">
        <div class="masthead-meta">
            {{ __('Report no.') }} <b>{{ $reportNo }}</b><br>
            {{ __('Issued') }} <b>{{ now()->format('d M Y') }}</b>
        </div>
        <img src="{{ public_path('images/logo-mark-print.jpg') }}" alt=""><span class="brand">SELFCURE PKS</span>
    </div>

    <div class="eyebrow">{{ __('Cybersecurity Self-Assessment Report') }}</div>
    <h1 class="title">{{ $business }}</h1>
    <p class="details">
        {{ __('Prepared for') }}: <b>{{ $user->name }}</b>
        @if ($user->business_type || $user->state)
            &nbsp;&middot;&nbsp; {{ collect([$user->business_type ? __($user->business_type) : null, $user->state])->filter()->join(', ') }}
        @endif
        @if ($user->ssm_number)
            &nbsp;&middot;&nbsp; {{ __('SSM number') }}: <b>{{ $user->ssm_number }}</b>
        @endif
        <br>
        {{ __('Assessment date') }}: <b>{{ $assessment->completed_at->format('d M Y') }}</b>
        &nbsp;&middot;&nbsp; {{ __('17 questions across the 5 areas of the NIST Cybersecurity Framework, answered by the business owner') }}
    </p>

    @php $n = 1; @endphp

    {{-- Summary --}}
    <div class="section">
        <h2><span class="n">{{ $n++ }}</span>{{ __('Summary') }}</h2>
        <div class="key-facts">
            <p><span class="k">{{ __('Overall score') }}:</span> <span class="v">{{ $assessment->overall_score }} / 100</span></p>
            <p><span class="k">{{ __('Maturity level') }}:</span> <span class="v">{{ __($assessment->overall_level) }}</span></p>
            <p><span class="k">{{ __('Risk level') }}:</span> <span class="v">{{ __($riskLevel) }}</span></p>
        </div>
        <p>
            {{ __(':business scored :score out of 100, which places the business at the :level level.', ['business' => $business, 'score' => $assessment->overall_score, 'level' => __($assessment->overall_level)]) }}
            {{ $levelMeaning[$assessment->overall_level] ?? '' }}
        </p>
        {{-- Only compare areas when their scores actually differ (not when all are tied, e.g. 100%). --}}
        @if ($strongest && $weakest && (float) $strongest->score > (float) $weakest->score)
            <p>
                {{ __('The strongest area is :strong (:strong_score%). The area that needs the most work is :weak (:weak_score%).', [
                    'strong' => $strongest->domain->translatedName(), 'strong_score' => number_format((float) $strongest->score, 1),
                    'weak' => $weakest->domain->translatedName(), 'weak_score' => number_format((float) $weakest->score, 1),
                ]) }}
            </p>
        @endif
        <p>
            @if ($recommendations->isEmpty())
                {{ __('None of the answers fell into the gap range, so there are no actions to take right now. Review your practices again in six months.') }}
            @else
                {{ trans_choice('{1} One answer showed a gap that needs fixing.|[2,*] :count answers showed gaps that need fixing.', $recommendations->count(), ['count' => $recommendations->count()]) }}
                @if ($highCount > 0)
                    {{ trans_choice('{1} One of them is high priority.|[2,*] :count of them are high priority.', $highCount, ['count' => $highCount]) }}
                @endif
                {{ __('The step-by-step actions for each gap are in your Action Plan on SELFCURE PKS.') }}
            @endif
        </p>
    </div>

    {{-- Reviewer's notes from the administrator, if any --}}
    @if ($assessment->auditor_notes)
        <div class="section" style="page-break-inside: avoid;">
            <h2><span class="n">{{ $n++ }}</span>{{ __("Reviewer's notes") }}</h2>
            <p>{!! nl2br(e($assessment->auditor_notes)) !!}</p>
        </div>
    @endif

    {{-- Results by area --}}
    <div class="section">
        <h2><span class="n">{{ $n++ }}</span>{{ __('Results by area') }}</h2>
        @foreach ($domainScores as $ds)
            <div class="area">
                <span class="area-name">{{ $ds->domain->translatedName() }}</span>
                &nbsp;&ndash;&nbsp; {{ number_format((float) $ds->score, 1) }}%
                &nbsp;&middot;&nbsp; {{ __('Maturity level') }}: {{ __($ds->level) }}
                <p class="area-desc">{{ $ds->domain->translatedDescription() }}</p>
            </div>
        @endforeach
        <p class="small muted">{{ __('Each area score is the average of its answers, converted to a percentage (an answer of 1 counts as 0% and 5 as 100%). The overall score is the average of the five areas.') }}</p>
    </div>

    {{-- Next steps --}}
    <div class="section" style="page-break-inside: avoid;">
        <h2><span class="n">{{ $n++ }}</span>{{ __('Next steps') }}</h2>
        <ol>
            <li>{{ __('Log in to SELFCURE PKS and open your Action Plan. Start with the high-priority actions and tick off each step as you go; your progress is saved automatically.') }}</li>
            <li>{{ __('Share this report with anyone who helps run the business, so everyone knows what is changing and why.') }}</li>
            <li>{{ __('Retake the assessment once the actions are done, or in six months at the latest, to see how your score has changed.') }}</li>
        </ol>
        <p class="about">{{ __('About this report: the results come from the answers the business gave in a self-assessment, not from a technical audit. It is practical guidance for everyday security, not legal advice.') }}</p>
    </div>

</body>
</html>
