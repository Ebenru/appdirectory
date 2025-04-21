{{-- resources/views/components/ui/card-title.blade.php --}}
<h3 {{ $attributes->merge(['class' => 'text-lg font-semibold leading-none tracking-tight']) }}>
    {{ $slot }}
</h3>