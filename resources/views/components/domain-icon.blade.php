@props(['code', 'size' => 20])

{{-- One stroke weight for every domain. Avoids the obvious metaphors (no shield for Protect). --}}
<svg {{ $attributes }} xmlns="http://www.w3.org/2000/svg" width="{{ $size }}" height="{{ $size }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($code)
        @case('identify')
            <rect x="5" y="4" width="14" height="17" rx="2"></rect><path d="M9 9h6M9 13h6M9 17h3"></path>
            @break
        @case('protect')
            <rect x="4" y="10" width="16" height="11" rx="2"></rect><path d="M8 10V7a4 4 0 0 1 8 0v3"></path><path d="M12 14v3"></path>
            @break
        @case('detect')
            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path><circle cx="12" cy="12" r="3"></circle>
            @break
        @case('respond')
            <path d="M13 2 3 14h9l-1 8 10-12h-9z"></path>
            @break
        @default
            <path d="M3 12a9 9 0 1 0 3-6.7L3 8"></path><path d="M3 3v5h5"></path>
    @endswitch
</svg>
