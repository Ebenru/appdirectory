{{-- resources/views/partials/_comment.blade.php --}}
@props(['comment', 'level' => 0]) {{-- Level for indentation --}}

<div class="flex space-x-3 {{ $level > 0 ? 'ml-'.($level * 4) : '' }}" id="comment-{{ $comment->id }}"> {{-- Dynamic margin for replies --}}
    {{-- User Avatar (Optional) --}}
    {{-- <div class="flex-shrink-0">
        <img class="h-8 w-8 rounded-full" src="{{ $comment->user->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($comment->user->name).'&color=7F9CF5&background=EBF4FF' }}" alt="{{ $comment->user->name }}">
    </div> --}}
    <div class="flex-1 space-y-1">
        <div class="flex items-center justify-between">
            <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ $comment->user->name }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400" title="{{ $comment->created_at->format('Y-m-d H:i:s') }}">
                {{ $comment->created_at->diffForHumans() }}
            </p>
        </div>
        <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $comment->text }}</p>

        {{-- Actions: Reply & Like --}}
        <div class="flex items-center space-x-4 text-xs pt-1">
            @auth
                {{-- Like Button for Comment --}}
                <form method="POST" action="{{ route('likes.toggle', ['type' => 'comment', 'id' => $comment->id]) }}" class="inline-flex">
                    @csrf
                    <button type="submit" class="flex items-center gap-1 text-muted-foreground hover:text-primary {{ Auth::user()->hasLiked($comment) ? 'text-destructive hover:text-destructive-hover' : '' }}">
                        @if(Auth::user()->hasLiked($comment))
                            <x-lucide-heart-crack class="w-3 h-3"/> Liked
                        @else
                            <x-lucide-heart class="w-3 h-3"/> Like
                        @endif
                        <span>({{ $comment->like_count }})</span>
                    </button>
                </form>

                {{-- Reply Button --}}
                <button type="button" onclick="toggleReplyForm('{{ $comment->id }}')" class="flex items-center gap-1 text-muted-foreground hover:text-primary">
                    <x-lucide-reply class="w-3 h-3"/> Reply
                </button>
            @else {{-- Show only like count if not logged in --}}
                 <span class="flex items-center gap-1 text-muted-foreground">
                     <x-lucide-heart class="w-3 h-3"/> {{ $comment->like_count }}
                 </span>
            @endauth
        </div>

         {{-- ******************************************* --}}
         {{-- *** START: INCLUDE REPLY FORM (FIXED) *** --}}
         {{-- ******************************************* --}}
         @auth
            <div id="reply-form-{{ $comment->id }}" class="hidden pt-3 border-l-2 border-gray-200 dark:border-gray-700 pl-3 mt-2">
                 {{-- Include the comment form partial, passing necessary data --}}
                 @include('partials._comment_form', [
                    'commentable_id' => $comment->commentable_id,        // The ID of the original Person/Company
                    'commentable_type' => $comment->commentable_type,    // The type ('person' or 'company')
                    'parent_id' => $comment->id,                          // The ID of the comment being replied TO
                    'placeholder' => 'Write a reply...'
                 ])
            </div>
         @endauth
         {{-- ******************************************* --}}
         {{-- *** END: INCLUDE REPLY FORM (FIXED) *** --}}
         {{-- ******************************************* --}}


        {{-- Recursively include replies --}}
        @if($comment->replies->isNotEmpty())
            <div class="pt-4 space-y-4"> {{-- Add padding/space before replies --}}
                {{-- @php --}}
                {{--     // Potentially sort replies here if needed, e.g., by likes or date --}}
                {{--     $sortedReplies = $comment->replies->sortByDesc('created_at'); // Or sortByDesc('like_count') after calculating it --}}
                {{-- @endphp --}}
                @foreach($comment->replies as $reply)
                    {{-- Recursive call --}}
                    @include('partials._comment', ['comment' => $reply, 'level' => $level + 1])
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Basic JS for toggling reply form - ensure it's pushed only once --}}
@pushOnce('scripts')
    <script>
        function toggleReplyForm(commentId) {
            const form = document.getElementById(`reply-form-${commentId}`);
            if (form) {
                 // Close other potentially open reply forms first (optional) 
                 document.querySelectorAll('[id^="reply-form-"]').forEach(f => {
                     if (f.id !== `reply-form-${commentId}`) {
                         f.classList.add('hidden');
                     }
                 });

                form.classList.toggle('hidden');
                if (!form.classList.contains('hidden')) {
                    // Optional: Focus the textarea when shown
                    const textarea = form.querySelector('textarea');
                    if (textarea) textarea.focus();
                }
            }
        }
    </script>
@endPushOnce