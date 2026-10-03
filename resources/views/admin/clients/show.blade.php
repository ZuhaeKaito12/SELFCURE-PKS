<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm mb-3">&larr; {{ __('Back to Admin Directory') }}</a>
        <div class="d-flex justify-content-between align-items-end flex-wrap gap-2">
            <div>
                <h2 class="fs-3 fw-bold mb-0">{{ $client->business_name ?? $client->name }}</h2>
                <p class="text-muted mb-0">{{ $client->name }} &middot; {{ $client->email }}</p>
            </div>
            @if ($latestAssessment)
                <a href="{{ route('admin.assessments.report', $latestAssessment) }}" class="btn btn-primary">{{ __('Regenerate Audit PDF') }}</a>
            @endif
        </div>
    </x-slot>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-2">{{ __('Send Notification') }}</h5>
            <p class="text-muted small mb-2">{{ __('This appears in the notification bell on their account.') }}</p>

            <form method="POST" action="{{ route('admin.clients.notify', $client) }}" class="d-flex gap-2 flex-wrap">
                @csrf
                <div class="input-group" style="max-width: 480px;">
                    <input type="text" id="notifyMessage" name="message" class="form-control" placeholder="{{ __('Type a message for this SME owner...') }}" maxlength="1000" required aria-label="{{ __('Send Notification') }}">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="{{ __('Suggested messages') }}" title="{{ __('Suggested messages') }}"></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @foreach ([
                            __('Please review your data backup policy.'),
                            __('Please resolve the outstanding items in your Action Plan.'),
                            __('Please retake your assessment to reflect recent changes.'),
                            __('Please update your business profile information.'),
                            __('Great progress on your cybersecurity posture — keep it up!'),
                        ] as $suggestion)
                            <li><button type="button" class="dropdown-item notify-suggestion">{{ $suggestion }}</button></li>
                        @endforeach
                    </ul>
                </div>
                <button type="submit" class="btn btn-outline-primary">{{ __('Send') }}</button>
            </form>
        </div>
    </div>

    <ul class="nav nav-pills mb-4" id="clientTabs">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#breakdown-tab" type="button">{{ __('Detailed Audit Breakdown') }}</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#action-plan-tab" type="button">{{ __('Action Plan') }}</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#profile-tab" type="button">{{ __('Company Profile') }}</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#history-tab" type="button">{{ __('Assessment History') }}</button>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="breakdown-tab">
            @if (! $latestAssessment)
                <div class="alert alert-info">{{ __('This client has not completed an assessment yet.') }}</div>
            @else
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0">{{ __('Administrator Recommendations') }}</h5>
                        </div>
                        <form method="POST" action="{{ route('admin.assessments.notes', $latestAssessment) }}">
                            @csrf
                            @method('patch')
                            <textarea name="auditor_notes" rows="4" class="form-control mb-2" placeholder="{{ __('Add manual audit notes or recommendations for this SME...') }}">{{ old('auditor_notes', $latestAssessment->auditor_notes) }}</textarea>
                            <button type="submit" class="btn btn-outline-primary btn-sm">{{ __('Save Auditor Notes') }}</button>
                        </form>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <span class="badge-tracked maturity-Managed d-inline-block mb-1">{{ __('Overall Score') }}</span>
                        <p class="fs-3 fw-bold mb-0">{{ $latestAssessment->overall_score }}% &middot; {{ __($latestAssessment->overall_level) }}</p>
                    </div>
                </div>

                @foreach ($breakdown as $item)
                    <div class="card shadow-sm mb-2">
                        <div class="card-body d-flex justify-content-between align-items-center gap-3">
                            <div>
                                <span class="text-muted small text-uppercase">{{ $item['domain'] }}</span>
                                <p class="mb-0">{{ $item['text'] }}</p>
                            </div>
                            <span class="badge maturity-{{ $item['status'] === 'Satisfying' ? 'Optimized' : ($item['status'] === 'Developing' ? 'Developing' : 'Initial') }} flex-shrink-0">{{ __($item['status']) }}</span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <div class="tab-pane fade" id="action-plan-tab">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Action Plan Progress') }}</h5>
                    @if ($actionPlanTasks->isEmpty())
                        <p class="text-muted mb-0">{{ __('No action items yet for this client.') }}</p>
                    @else
                        @php $planPercent = round(($actionPlanResolved / $actionPlanTasks->count()) * 100); @endphp
                        <p class="text-muted small mb-2">{{ __(':completed of :total actions completed', ['completed' => $actionPlanResolved, 'total' => $actionPlanTasks->count()]) }}</p>
                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $planPercent }}%"></div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>{{ __('Action') }}</th>
                                        <th>{{ __('Area') }}</th>
                                        <th>{{ __('Priority') }}</th>
                                        <th>{{ __('Steps') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($actionPlanTasks as $task)
                                        <tr>
                                            <td class="fw-semibold">{{ $task->action->translatedTitle() }}</td>
                                            <td class="text-muted small">{{ $task->action->question->domain->translatedName() }}</td>
                                            <td><span class="badge priority-chip priority-{{ $task->priority() }}">{{ $task->priority() === 'high' ? __('High') : __('Medium') }}</span></td>
                                            <td class="text-muted small">{{ count($task->doneSteps()) }}/{{ $task->stepCount() }}</td>
                                            <td><x-action-status :status="$task->status" /></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="profile-tab">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('SME Registration & Corporate Details') }}</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="text-muted small text-uppercase">{{ __('Business Name') }}</div>
                                <div class="fw-medium">{{ $client->business_name ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="text-muted small text-uppercase">{{ __('SSM Number') }}</div>
                                <div class="fw-medium">{{ $client->ssm_number ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="text-muted small text-uppercase">{{ __('Industry') }}</div>
                                <div class="fw-medium">{{ $client->business_type ? __($client->business_type) : '—' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="text-muted small text-uppercase">{{ __('State') }}</div>
                                <div class="fw-medium">{{ $client->state ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="text-muted small text-uppercase">{{ __('Owner & Phone') }}</div>
                                <div class="fw-medium">{{ $client->name }} &middot; {{ $client->phone ?? '—' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <div class="text-muted small text-uppercase">{{ __('Email') }}</div>
                                <div class="fw-medium">{{ $client->email }}</div>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('admin.clients.edit', $client) }}" class="btn btn-outline-secondary btn-sm mt-3">{{ __('Edit Corporate Details') }}</a>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="history-tab">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">{{ __('Completed Audit Reports Log') }}</h5>
                    @php $completed = $client->assessments->where('status', 'completed'); @endphp
                    @if ($completed->isEmpty())
                        <p class="text-muted mb-0">{{ __('No completed assessments yet.') }}</p>
                    @else
                        @foreach ($completed as $assessment)
                            <div class="card shadow-sm mb-2">
                                <div class="card-body d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-medium">{{ $assessment->completed_at->format('d M Y, h:i A') }}</div>
                                        <div class="text-muted small">{{ __('Score:') }} {{ $assessment->overall_score }}% &middot; {{ __($assessment->overall_level) }}</div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.assessments.show', $assessment) }}" class="btn btn-outline-primary btn-sm">{{ __('View Result') }}</a>
                                        <a href="{{ route('admin.assessments.report', $assessment) }}" class="btn btn-outline-secondary btn-sm">{{ __('View PDF') }}</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <script>
            document.querySelectorAll('.notify-suggestion').forEach((item) => {
                item.addEventListener('click', () => {
                    const input = document.getElementById('notifyMessage');
                    input.value = item.textContent.trim();
                    input.focus();
                });
            });
        </script>
    </x-slot>
</x-app-layout>
