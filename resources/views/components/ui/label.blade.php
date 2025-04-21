{{-- resources/views/components/ui/label.blade.php --}}
@props([
    'for' => null, // Optional 'for' attribute to link to an input ID
])

<label
    @if($for) for="{{ $for }}" @endif
    {{ $attributes->merge(['class' => 'text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70']) }}
>
    {{ $slot }} {{-- The label text goes here --}}
</label>