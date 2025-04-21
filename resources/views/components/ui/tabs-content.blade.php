{{-- resources/views/components/ui/tabs-content.blade.php --}}
@props([
    // The unique identifier for this content, must match the corresponding trigger's value
    'value',
])

{{--
    x-show makes this div visible only when 'activeTab' matches this component's 'value'.
    x-cloak prevents a flash of unstyled/unhidden content before Alpine initializes.
    role="tabpanel" and :aria-labelledby improve accessibility.
--}}
<div
    x-show="activeTab === '{{ $value }}'"
    x-cloak
    role="tabpanel"
    tabindex="0"
    aria-labelledby="tab-trigger-{{ $value }}" {{-- Match with trigger ID potentially --}}
    {{ $attributes->merge(['class' => 'mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2']) }}
    style="display: none;" {{-- Add style none as x-cloak fallback --}}
>
    {{ $slot }}
</div>