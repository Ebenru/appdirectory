{{-- resources/views/components/ui/tabs.blade.php --}}
@props([
    // The 'value' of the tab that should be active by default
    'defaultValue'
])

{{--
    Initializes Alpine.js data for the tabs component.
    'activeTab' holds the 'value' of the currently selected tab trigger.
--}}
<div
    x-data="{ activeTab: '{{ $defaultValue }}' }"
    {{ $attributes }}
>
    {{ $slot }}
</div>