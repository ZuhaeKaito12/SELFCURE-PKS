<x-app-layout>
    <x-slot name="header">
        <h2 class="fs-4 fw-semibold mb-0">{{ __('All Assessment Records') }}</h2>
    </x-slot>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <form method="GET" class="d-flex gap-2 align-items-center">
                <label for="status" class="form-label mb-0 small text-muted">{{ __('Filter:') }}</label>
                <select name="status" id="status" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                    <option value="">{{ __('All') }}</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>{{ __('In Progress') }}</option>
                </select>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @if ($assessments->isEmpty())
                <p class="text-muted mb-0">{{ __('No assessment records found.') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('Client') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Completed') }}</th>
                                <th>{{ __('Score') }}</th>
                                <th>{{ __('Level') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assessments as $assessment)
                                <tr>
                                    <td>{{ $assessment->user->business_name ?? $assessment->user->name }}</td>
                                    <td><span class="badge {{ $assessment->status === 'completed' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $assessment->status === 'completed' ? __('Completed') : __('In Progress') }}</span></td>
                                    <td>{{ $assessment->completed_at?->format('d M Y, h:i A') ?? '—' }}</td>
                                    <td>{{ $assessment->overall_score ?? '—' }}{{ $assessment->overall_score !== null ? '%' : '' }}</td>
                                    <td>{{ $assessment->overall_level ? __($assessment->overall_level) : '—' }}</td>
                                    <td class="text-end">
                                        @if ($assessment->status === 'completed')
                                            <a href="{{ route('admin.assessments.show', $assessment) }}" class="btn btn-outline-primary btn-sm">{{ __('View') }}</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $assessments->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
