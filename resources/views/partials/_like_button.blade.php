{{-- resources/views/partials/_like_button.blade.php --}}
@props(['likeable', 'type']) {{-- Expects the model instance and type string --}}

@auth
<form method="POST" action="{{ route('likes.toggle', ['type' => $type, 'id' => $likeable->id]) }}" class="inline-flex">
    @csrf
    <x-ui.button type="submit" variant="{{ Auth::user()->hasLiked($likeable) ? 'destructive' : 'outline' }}" size="sm" class="flex items-center gap-1">
        @if(Auth::user()->hasLiked($likeable))
        <x-lucide-heart-crack class="w-4 h-4" /> Liked
        @else
        <x-lucide-heart class="w-4 h-4" /> Like
        @endif
        <span>({{ $likeable->like_count }})</span>
    </x-ui.button>
</form>
@else
<span class="text-sm text-muted-foreground flex items-center gap-1 p-2 border rounded-md">
    <x-lucide-heart class="w-4 h-4" /> {{ $likeable->like_count }} Likes
</span>
@endauth