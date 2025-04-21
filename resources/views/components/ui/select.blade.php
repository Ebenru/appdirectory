{{-- resources/views/components/ui/select.blade.php --}}
@props([
    'disabled' => false,
])

{{-- Note: Styling native <select> elements is limited across browsers. --}}
{{-- This provides basic styling matching the input fields but won't replicate --}}
{{-- the custom dropdown appearance of JavaScript-based components like Shadcn's. --}}
<select
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge(['class' => 'flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50']) !!}
>
    {{ $slot }} {{-- Place your <option> elements here --}}
</select>