{{-- resources/views/components/ui/button.blade.php --}}
@props([
'type' => 'button',
'variant' => 'default', // Added variant prop with default value
'disabled' => false,
])

@php
// Determine the variant-specific classes based on the 'variant' prop
$variantClasses = match ($variant) {
'destructive' => 'bg-destructive text-destructive-foreground hover:bg-destructive/90',
'destructive-outline' => 'border border-border bg-transparent text-destructive hover:bg-destructive/10 hover:text-accent-foreground',
'outline' => 'border border-input bg-background hover:bg-accent hover:text-accent-foreground',
'secondary' => 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
'ghost' => 'hover:bg-accent hover:text-accent-foreground',
'link' => 'text-primary underline-offset-4 hover:underline',
default => 'bg-primary text-primary-foreground hover:bg-primary/90', // Default variant
};

// Base classes common to all variants
$baseClasses = 'inline-flex items-center justify-center whitespace-nowrap rounded-md text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none';

// Default size classes (can be overridden by passing size classes directly)
// Example: <x-ui.button class="h-9 px-3">Small</x-ui.button>
// You could also add a 'size' prop if preferred
$sizeClasses = 'h-10 py-2 px-4';

@endphp

<button
    type="{{ $type }}"
    {{ $disabled ? 'disabled' : '' }}
    {{-- Merge base, size, variant, and any additional classes passed via attributes --}}
    {{ $attributes->class([
        $baseClasses,
        $sizeClasses,
        $variantClasses,
    ]) }}>
    {{ $slot }} {{-- Button text/content goes here --}}
</button>