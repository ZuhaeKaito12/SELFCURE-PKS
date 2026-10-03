@props(['status'])

<span {{ $attributes->merge(['class' => 'badge status-badge status-'.$status]) }}>
    {{ match ($status) {
        'completed' => __('Completed'),
        'in_progress' => __('In Progress'),
        default => __('Not Started'),
    } }}
</span>
