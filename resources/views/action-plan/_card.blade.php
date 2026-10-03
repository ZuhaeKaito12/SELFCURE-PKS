@php
    $action = $item->action;
    $question = $action->question;
    $steps = $action->translatedSteps();
    $done = $item->doneSteps();
    $priority = $item->priority();
    $scaleLabels = \App\Services\MaturityScoringService::scaleLabels();
@endphp

<article class="action-card priority-{{ $priority }} {{ $item->isCompleted() ? 'is-completed' : '' }}"
         id="action-{{ $item->id }}"
         data-status="{{ $item->status }}"
         data-status-url="{{ route('action-plan.update', $item) }}"
         data-steps-url="{{ route('action-plan.steps', $item) }}">
    <h3 class="m-0 fs-6 fw-normal">
        <button type="button" class="action-card-head js-toggle" aria-expanded="false" aria-controls="action-panel-{{ $item->id }}">
            <span class="action-icon"><x-domain-icon :code="$question->domain->code" :size="18" /></span>
            <span class="d-block min-w-0">
                <span class="d-flex flex-wrap align-items-center gap-2">
                    <span class="badge priority-chip priority-{{ $priority }}">{{ $priority === 'high' ? __('High priority') : __('Medium priority') }}</span>
                    <span class="domain-pill">{{ $question->domain->translatedName() }}</span>
                    <x-action-status :status="$item->status" class="js-status-badge" />
                </span>
                <span class="action-title d-block">{{ $action->translatedTitle() }}</span>
                <span class="action-desc d-block">{{ $action->translatedDescription() }}</span>
            </span>
            <span class="action-side">
                <span class="action-mini" aria-hidden="true">
                    <span><span class="js-steps-done-mini">{{ count($done) }}</span>/{{ count($steps) }} {{ __('steps') }}</span>
                    <span class="progress progress-scale d-block"><span class="progress-bar d-block js-progress-mini" style="transform: scaleX({{ $item->progressPercent() / 100 }});"></span></span>
                </span>
                <svg class="action-chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
            </span>
        </button>
    </h3>

    <div class="action-panel" id="action-panel-{{ $item->id }}">
        <div class="action-body">
            <div class="action-why">
                <span class="action-label">{{ __('Why this matters') }}</span>
                <p class="mb-0">{{ $question->translatedGapDescription() }}</p>
            </div>

            <div class="action-steps">
                <div class="d-flex justify-content-between align-items-baseline mb-1">
                    <span class="action-label mb-0">{{ __('Recommended steps') }}</span>
                    <span class="small text-muted sp-mono"><span class="js-steps-done">{{ count($done) }}</span>/{{ count($steps) }} {{ __('done') }}</span>
                </div>
                <ul class="list-unstyled mb-3">
                    @foreach ($steps as $index => $step)
                        <li>
                            <label class="action-step">
                                <input type="checkbox" class="form-check-input js-step" data-step="{{ $index }}" {{ in_array($index, $done, true) ? 'checked' : '' }}>
                                <span>{{ $step }}</span>
                            </label>
                        </li>
                    @endforeach
                </ul>
                <div class="progress progress-scale" aria-hidden="true">
                    <div class="progress-bar js-progress" style="transform: scaleX({{ $item->progressPercent() / 100 }});"></div>
                </div>
            </div>
        </div>

        <div class="action-card-foot">
            <form method="POST" action="{{ route('action-plan.update', $item) }}" class="status-switch js-status-form" role="group" aria-label="{{ __('Action status') }}">
                @csrf
                @method('PATCH')
                @foreach (['not_started' => __('Not Started'), 'in_progress' => __('In Progress'), 'completed' => __('Completed')] as $value => $label)
                    <button type="submit" name="status" value="{{ $value }}"
                            class="status-option {{ $item->status === $value ? 'is-active' : '' }}"
                            aria-pressed="{{ $item->status === $value ? 'true' : 'false' }}">{{ $label }}</button>
                @endforeach
            </form>

            <details class="action-source">
                <summary>{{ __('Based on your assessment') }}</summary>
                <p class="mb-1">&ldquo;{{ $question->translatedText() }}&rdquo;</p>
                @if ($item->answer_score)
                    <p class="mb-1">{{ __('Your answer') }}: <strong>{{ $item->answer_score }} &middot; {{ $scaleLabels[$item->answer_score] ?? '' }}</strong>@if ($item->assessment?->completed_at) &middot; {{ $item->assessment->completed_at->format('d M Y') }}@endif</p>
                @endif
                <p class="mb-0">{{ __('Related regulation:') }} {{ $question->regulation_reference }}</p>
            </details>
        </div>
    </div>
</article>
