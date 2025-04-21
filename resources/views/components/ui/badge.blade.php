{{-- resources/views/components/ui/badge.blade.php --}}
@props([
    'variant' => 'default' // Default variant style
])

@php
    // Determine classes based on the variant prop
    $variantClasses = match ($variant) {
        'secondary' => 'border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80',
        'destructive' => 'border-transparent bg-destructive text-destructive-foreground hover:bg-destructive/80',
        'outline' => 'text-foreground', // Uses default border color defined by '*' in app.css
        default => 'border-transparent bg-primary text-primary-foreground hover:bg-primary/80', // Default uses primary
    };
@endphp

<span {{ $attributes->class([
    // Base badge styles
    'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors',
    // Focus styles (using ring variable from app.css)
    'focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2',
    // Variant specific styles
    $variantClasses
]) }}>
    {{ $slot }} {{-- The text content of the badge --}}
</span>