{{-- resources/views/partials/_comment_form.blade.php --}}
{{-- Expects $commentable_id, $commentable_type, and optionally $parent_id --}}
@props([
    'commentable_id',
    'commentable_type', // 'person' or 'company' as string
    'parent_id' => null, // Null for top-level, comment ID for replies
    'placeholder' => 'Add your comment...'
])

<form method="POST" action="{{ route('comments.store') }}" class="space-y-3">
    @csrf
    <input type="hidden" name="commentable_id" value="{{ $commentable_id }}">
    <input type="hidden" name="commentable_type" value="{{ $commentable_type }}">
    @if($parent_id)
        <input type="hidden" name="parent_id" value="{{ $parent_id }}">
    @endif

    <div>
        {{-- Use Label component or just a placeholder --}}
        <x-ui.textarea
            id="comment-text-{{ $parent_id ?? 'new' }}"
            name="text"
            rows="3"
            placeholder="{{ $placeholder }}"
            required
            class="mt-1 block w-full"
        ></x-ui.textarea>
        {{-- Display validation errors if needed --}}
        {{-- <x-input-error :messages="$errors->get('text')" class="mt-2" /> --}}
        {{-- **** ADD THIS BLOCK TO SHOW ERRORS **** --}}
        @error('text')
            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
        {{-- You could also add errors for hidden fields if needed, though less common --}}
        @error('parent_id')
            <p class="mt-1 text-xs text-red-600 dark:text-red-400">Error associating reply: {{ $message }}</p>
        @enderror
         @error('commentable_id')
            <p class="mt-1 text-xs text-red-600 dark:text-red-400">Error associating comment: {{ $message }}</p>
        @enderror
         @error('commentable_type')
            <p class="mt-1 text-xs text-red-600 dark:text-red-400">Error associating comment: {{ $message }}</p>
        @enderror
        {{-- **** END ERROR BLOCK **** --}}
    </div>

    <div class="flex justify-end">
        <x-ui.button type="submit" size="sm">
            {{ $parent_id ? 'Post Reply' : 'Post Comment' }}
        </x-ui.button>
    </div>
</form>