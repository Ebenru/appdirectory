{{-- resources/views/components/ui/input.blade.php --}}
@props([
    'disabled' => false,
    'type' => 'text' // Default type is text
])

<input
    type="{{ $type }}"
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge(['class' => 'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50']) !!}
>