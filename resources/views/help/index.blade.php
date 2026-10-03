<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="badge-tracked maturity-Managed d-inline-block mb-2">{{ __('Help Centre') }}</span>
                <h2 class="fs-4 fw-semibold mb-0">{{ __('User Manual') }}</h2>
                <p class="text-muted mb-0">{{ __('A quick guide to using SELFCURE PKS, step by step.') }}</p>
            </div>
            <a href="{{ route($isAdmin ? 'admin.dashboard' : 'dashboard') }}?tour=1" class="btn btn-outline-primary">
                {{ __('Replay Guided Tour') }}
            </a>
        </div>
    </x-slot>

    @if (! $isAdmin)
        <section class="card next-step-card mb-4">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <span class="badge-tracked">{{ __('Guided tours') }}</span>
                    <h3 class="fs-5 mt-2 mb-1">{{ __('Let SELFCURE PKS walk you through each page, one feature at a time.') }}</h3>
                    <p class="text-muted small mb-0">{{ __('Tip: click the ? button at the top of any page to see the tour for that page.') }}</p>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('dashboard') }}?tour=1" class="btn btn-primary">{{ __('Dashboard tour') }}</a>
                    <a href="{{ route('action-plan.index') }}?tour=1" class="btn btn-outline-primary">{{ __('Action Plan tour') }}</a>
                    <a href="{{ route('assessment.history') }}?tour=1" class="btn btn-outline-primary">{{ __('Assessment History tour') }}</a>
                </div>
            </div>
        </section>

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <span class="badge-tracked maturity-Managed d-inline-block mb-2">{{ __('Step 1') }}</span>
                        <h5 class="card-title">{{ __('Start Your Assessment') }}</h5>
                        <p class="text-muted small mb-0">{{ __('From your dashboard, click "Start Assessment" to begin the 17-question questionnaire. It is split across the 5 cybersecurity domains: Identify, Protect, Detect, Respond and Recover. If you leave halfway, your answers are saved automatically — click "Resume Assessment" to continue later.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <span class="badge-tracked maturity-Managed d-inline-block mb-2">{{ __('Step 2') }}</span>
                        <h5 class="card-title">{{ __('Answer the Questions') }}</h5>
                        <p class="text-muted small mb-0">{{ __('Each question is written in plain language with a 1-5 scale, from "Not in place" to "Fully in place." Answer honestly based on your current business practices — there are no right or wrong answers, only an accurate picture of where you stand today.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <span class="badge-tracked maturity-Managed d-inline-block mb-2">{{ __('Step 3') }}</span>
                        <h5 class="card-title">{{ __('View Your Cyber Maturity Score') }}</h5>
                        <p class="text-muted small mb-0">{{ __('Once submitted, your dashboard shows your Overall Score, Maturity Level (Initial, Developing, Managed or Optimized) and a breakdown across all 5 domains. Click "View Result & Radar" for the full radar chart and gap analysis.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <span class="badge-tracked maturity-Developing d-inline-block mb-2">{{ __('Step 4') }}</span>
                        <h5 class="card-title">{{ __('Work Through Your Action Roadmap') }}</h5>
                        <p class="text-muted small mb-0">{{ __('Every gap found in your assessment becomes a practical action, such as "Update Business Devices and Applications", with simple steps to follow. Tick off each step as you go and mark the action as Completed when it is done. Your progress updates automatically.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <span class="badge-tracked maturity-Managed d-inline-block mb-2">{{ __('Step 5') }}</span>
                        <h5 class="card-title">{{ __('Download Your Report') }}</h5>
                        <p class="text-muted small mb-0">{{ __('From your result page, download a PDF report of your assessment. It includes your score, radar chart and recommendations linked to the Cybersecurity Act 2024 and PDPA — useful for your own records or to share with your team.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <span class="badge-tracked maturity-Managed d-inline-block mb-2">{{ __('Step 6') }}</span>
                        <h5 class="card-title">{{ __('Reassess Over Time') }}</h5>
                        <p class="text-muted small mb-0">{{ __('Cyber risks change, so it is a good habit to retake the assessment every 6 months. SELFCURE PKS will remind you on your dashboard when a reassessment is due, and your Assessment History page keeps every past result.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row g-4 mb-4">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <span class="badge-tracked maturity-Managed d-inline-block mb-2">{{ __('Step 1') }}</span>
                        <h5 class="card-title">{{ __('Browse the SME Directory') }}</h5>
                        <p class="text-muted small mb-0">{{ __('Your dashboard lists every registered SME, with search and industry filters. The top metric cards summarise total SMEs, average score, high-risk count and completed audits across the whole system.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <span class="badge-tracked maturity-Managed d-inline-block mb-2">{{ __('Step 2') }}</span>
                        <h5 class="card-title">{{ __('Register a New SME') }}</h5>
                        <p class="text-muted small mb-0">{{ __('Click "+ Register New SME" to add a business that needs to be onboarded, so they can log in and complete their own self-assessment.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <span class="badge-tracked maturity-Managed d-inline-block mb-2">{{ __('Step 3') }}</span>
                        <h5 class="card-title">{{ __('Review an Audit Record') }}</h5>
                        <p class="text-muted small mb-0">{{ __('Click "Audit Record" next to any SME to see their full assessment: overall score, domain breakdown and action plan progress. You can also browse every submission under Assessment Records.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <span class="badge-tracked maturity-Developing d-inline-block mb-2">{{ __('Step 4') }}</span>
                        <h5 class="card-title">{{ __('Send Feedback to an SME') }}</h5>
                        <p class="text-muted small mb-0">{{ __('From an SME\'s record, you can send them a short feedback note. They will see it as a notification on their own dashboard.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <span class="badge-tracked maturity-Managed d-inline-block mb-2">{{ __('Step 5') }}</span>
                        <h5 class="card-title">{{ __('Check Risk Distribution') }}</h5>
                        <p class="text-muted small mb-0">{{ __('The Risk Distribution chart and Domain Performance breakdown on your dashboard give a system-wide view of which SMEs — and which domains — need the most attention.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <span class="badge-tracked maturity-Managed d-inline-block mb-2">{{ __('Step 6') }}</span>
                        <h5 class="card-title">{{ __('Export a Summary Report') }}</h5>
                        <p class="text-muted small mb-0">{{ __('Click "Export Summary Report" to download a PDF covering all registered SMEs, useful for reporting to stakeholders.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <span class="badge-tracked maturity-Managed d-inline-block mb-3">{{ __('Frequently Asked Questions') }}</span>
            <div class="accordion" id="helpFaqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            {{ __('What do the maturity levels (Initial, Developing, Managed, Optimized) mean?') }}
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#helpFaqAccordion">
                        <div class="accordion-body text-muted small">
                            {{ __('They describe how far along your cybersecurity practices are, from "Initial" (little or nothing in place) to "Optimized" (strong, consistently applied practices). The level is calculated from your overall score, and is shown for your business overall as well as for each of the 5 domains.') }}
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            {{ __('Can I retake the assessment?') }}
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#helpFaqAccordion">
                        <div class="accordion-body text-muted small">
                            {{ __('Yes. Click "Retake Assessment" from your dashboard at any time, or when SELFCURE PKS reminds you that a reassessment is due. All previous results remain available under Assessment History.') }}
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            {{ __('How is my Action Plan generated?') }}
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#helpFaqAccordion">
                        <div class="accordion-body text-muted small">
                            {{ __('The assessment asks about your current practices. Any question you answer with a low score points to a gap. SELFCURE PKS then creates a separate, practical action for that gap, with recommended steps and a priority, so you know exactly what to do instead of just seeing a score.') }}
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            {{ __('Can I see the guided tour again?') }}
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#helpFaqAccordion">
                        <div class="accordion-body text-muted small">
                            {{ __('Yes. Click "Replay Guided Tour" at the top of this page, or the "Replay Tour" button in the top bar on your dashboard, at any time.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
