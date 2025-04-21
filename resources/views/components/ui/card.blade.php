{{-- resources/views/components/ui/card.blade.php --}}
<div {{ $attributes->merge(['class' => 'rounded-lg border bg-card text-card-foreground shadow-sm']) }}>
    {{ $slot }}
</div>