{{-- resources/views/components/ui/tabs-trigger.blade.php --}}
@props([
    'value',
    'disabled' => false,
])

<button
    type="button"
    role="tab"
    :aria-selected="activeTab === '{{ $value }}'" {{-- Use : for attribute binding --}}
    :tabindex="activeTab === '{{ $value }}' ? '0' : '-1'" {{-- Use : for attribute binding --}}
    @click="activeTab = '{{ $value }}'"
    :disabled="{{ $disabled ? 'true' : 'false' }}" {{-- Use : for attribute binding --}}
    {{-- Corrected :class binding --}}
    :class="{
        'bg-background text-foreground shadow-sm': activeTab === '{{ $value }}',
        'text-muted-foreground hover:text-foreground': activeTab !== '{{ $value }}'
    }"
    {{-- Base classes are merged separately for clarity --}}
    {{ $attributes->merge([
        'class' => 'inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50'
    ]) }}
>
    {{ $slot }}
</button>