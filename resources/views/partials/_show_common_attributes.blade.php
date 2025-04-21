{{-- resources/views/partials/_show_common_attributes.blade.php --}}
{{-- Expects $entity variable --}}

{{-- Tags --}}
@if($entity->tags->isNotEmpty())
<div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Tags</dt>
    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
        <div class="flex flex-wrap gap-2">
            @foreach($entity->tags as $tag)
            {{-- TODO: Make tags linkable to a search/tag page --}}
            <x-ui.badge variant="secondary">{{ $tag->name }}</x-ui.badge>
            @endforeach
        </div>
    </dd>
</div>
@endif

{{-- Social Media --}}
@if(!empty($entity->social_media))
<div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Social Links</dt>
    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
        <div class="flex flex-wrap gap-4">
            @foreach($entity->social_media as $platform => $url)
            @if($url)
            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="text-muted-foreground hover:text-primary" title="{{ ucfirst($platform) }}">
                <x-dynamic-component :component="'lucide-' . strtolower($platform)" class="w-5 h-5" />
            </a>
            @endif
            @endforeach
        </div>
    </dd>
</div>
@endif

{{-- Sources --}}
@if(!empty($entity->sources))
<div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Sources</dt>
    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
        <ul class="list-disc list-inside space-y-1">
            @foreach($entity->sources as $sourceUrl)
            <li>
                <a href="{{ $sourceUrl }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline break-all" title="{{ $sourceUrl }}">
                    {{ Str::limit($sourceUrl, 50) }}
                </a>
            </li>
            @endforeach
        </ul>
    </dd>
</div>
@endif

{{-- Featured Article --}}
@if($entity->featured_article_url)
<div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Featured Link</dt>
    <dd class="mt-1 text-sm sm:mt-0 sm:col-span-2">
        <a href="{{ $entity->featured_article_url }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline break-all">
            {{ $entity->featured_article_url }} <x-lucide-external-link class="inline-block h-3 w-3 ml-1" />
        </a>
    </dd>
</div>
@endif

{{-- Submitted --}}
@if($entity->creator || $entity->created_at)
<div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Submitted</dt>
    <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300 sm:mt-0 sm:col-span-2">
        @if($entity->created_at) {{ $entity->created_at->format('M d, Y') }} @endif
        @if($entity->creator) by {{ $entity->creator->name }} @endif
    </dd>
</div>
@endif


{{-- Approved --}}
@if($entity->approved_at)
<div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Approved</dt>
    <dd class="mt-1 text-sm text-gray-700 dark:text-gray-300 sm:mt-0 sm:col-span-2">
        {{ $entity->approved_at->format('M d, Y') }}
        @if($entity->approvedBy) by {{ $entity->approvedBy->name }} @endif
    </dd>
</div>
@endif