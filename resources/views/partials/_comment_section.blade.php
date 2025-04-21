{{-- resources/views/partials/_comment_section.blade.php --}}
@props([
'commentable', // The main model instance (Person, Company, Group, etc.)
'type', // The morphMap type string ('person', 'company', 'group', etc.)
'comments', // The collection of sorted, top-level comments passed from the controller
'currentSort' => 'newest' // Default sort order
])

{{-- Wrapper div for spacing/styling if needed --}}
<div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
    {{-- Header Row --}}
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
            {{-- Calculate approximate total count --}}
            @php
            $totalCommentCount = $comments->count();
            foreach($comments as $c) { $totalCommentCount += $c->replies->count(); } // Simple count, might need recursive for deeper replies
            @endphp
            Discussion ({{ $totalCommentCount }})
        </h2>
        <div class="text-sm flex space-x-3">
            <span class="text-muted-foreground">Sort by:</span>
            {{-- Use current route with query parameters for sorting --}}
            <a href="{{ route(request()->route()->getName(), array_merge(request()->route()->parameters(), ['sort' => 'newest'])) }}"
                class="{{ $currentSort == 'newest' ? 'text-primary font-medium' : 'text-muted-foreground hover:text-primary' }}">Newest</a>
            <a href="{{ route(request()->route()->getName(), array_merge(request()->route()->parameters(), ['sort' => 'likes'])) }}"
                class="{{ $currentSort == 'likes' ? 'text-primary font-medium' : 'text-muted-foreground hover:text-primary' }}">Most Liked</a>
        </div>
    </div>

    {{-- Top-Level Comment Form --}}
    @auth
    @include('partials._comment_form', [
    'commentable_id' => $commentable->id,
    'commentable_type' => $type // Use the passed type string
    ])
    @else
    <p class="text-center text-muted-foreground py-4">
        <a href="{{ route('login') }}?redirect={{ url()->current() }}" class="text-primary hover:underline">Log in</a> to join the discussion.
    </p>
    @endauth

    {{-- Display Comments --}}
    <div class="mt-8 space-y-6">
        @forelse($comments as $comment) {{-- Use the passed $comments collection --}}
        @include('partials._comment', ['comment' => $comment])
        @empty
        <p class="text-center text-muted-foreground pt-4">Be the first to comment!</p>
        @endforelse
    </div>
</div>