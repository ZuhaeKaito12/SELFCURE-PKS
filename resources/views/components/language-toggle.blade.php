@props(['variant' => 'light'])

@php
    $target = app()->getLocale() === 'ms' ? 'en' : 'ms';
    $targetLabel = $target === 'ms' ? 'BM' : 'EN';
    $btnClass = $variant === 'dark' ? 'btn-outline-secondary text-white border-light' : 'btn-outline-secondary';
@endphp

<a href="{{ route('language.switch', ['locale' => $target, 'return' => request()->getRequestUri()]) }}"
   class="btn btn-sm {{ $btnClass }} d-inline-flex align-items-center gap-1"
   title="{{ $target === 'ms' ? 'Tukar ke Bahasa Melayu' : 'Switch to English' }}">
    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="2" y1="12" x2="22" y2="12"></line>
        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
    </svg>
    <span class="fw-semibold">{{ $targetLabel }}</span>
</a>
