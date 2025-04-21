{{-- resources/views/components/ui/card-content.blade.php --}}
<div {{ $attributes->merge(['class' => 'p-6 pt-0']) }}>
    {{ $slot }}
</div>