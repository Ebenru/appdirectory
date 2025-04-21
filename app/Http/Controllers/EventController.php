<?php

namespace App\Http\Controllers;

use App\Models\Event; // Import Event
use App\Models\Country;
use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // Import Trait
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\View\View;
use Carbon\Carbon; // Import Carbon for dates

class EventController extends Controller
{
    use AuthorizesRequests; // Use Trait

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // Basic status filtering
        $query = Event::query();
        if (!Auth::check() || !Auth::user()->is_admin) {
            $query->where('status', 'approved');
        }

        // Filter by upcoming/past?
        if ($request->input('filter') === 'upcoming') {
            $query->where('startDate', '>=', now());
        } elseif ($request->input('filter') === 'past') {
            $query->where('endDate', '<', now())->orWhere(fn($q) => $q->whereNull('endDate')->where('startDate', '<', now()));
        }

        // TODO: Add search/category(eventType) filtering

        $events = $query->with(['country', 'tags'])
            ->orderBy('startDate', 'desc') // Show upcoming/most recent first
            ->paginate(12)
            ->withQueryString();

        return view('events.index', [
            'events' => $events,
            'currentFilter' => $request->input('filter'),
            // Pass other filter options if implemented
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Event::class);

        $countries = Country::orderBy('name')->pluck('name', 'id');
        // TODO: Define Event types in Model or config
        $eventTypes = ['Awareness Campaign', 'Protest', 'Conference', 'Fundraiser', 'Workshop', 'Webinar', 'Other']; // Example

        return view('events.create', [
            'countries' => $countries,
            'eventTypes' => $eventTypes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Event::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'startDate' => ['required', 'date'],
            'endDate' => ['nullable', 'date', 'after_or_equal:startDate'],
            'location' => ['nullable', 'string', 'max:500'],
            'is_virtual' => ['sometimes', 'boolean'],
            'eventType' => ['nullable', 'string', 'max:100'],
            'featured_image_url' => ['nullable', 'url', 'max:500', Rule::requiredIf(!$request->hasFile('featured_image'))],
            'featured_image' => ['nullable', Rule::requiredIf(!$request->filled('featured_image_url')), File::image()->max(2048)],
            'website_url' => ['nullable', 'url', 'max:500'],
            'country_id' => ['nullable', 'integer', 'exists:countries,id'], // Nullable if purely virtual/global
            'tags' => ['nullable', 'string', 'max:500'],
            'social_media' => ['nullable', 'array'],
            'social_media.*' => ['nullable', 'url', 'max:500'],
            'sources' => ['nullable', 'string'],
            'key_achievements' => ['nullable', 'string', 'max:2000'], // Renamed for consistency
            // 'featured_article_url' => ['nullable', 'url', 'max:500'], // Maybe less relevant for events?
        ], [
            'featured_image_url.required_if' => 'Please provide an Image URL or upload an image.',
            'featured_image.required_if' => 'Please upload an image or provide an Image URL.',
            'social_media.*.url' => 'Each social media link must be a valid URL.',
        ]);

        // --- Handle Image Upload ---
        $imagePath = null;
        if ($request->hasFile('featured_image') && $request->file('featured_image')->isValid()) {
            $imagePath = $request->file('featured_image')->store('event-images', 'public');
            $validated['featured_image_url'] = null;
        }

        // --- Prepare JSON fields ---
        $sourcesInput = $request->input('sources');
        $sourcesArray = $sourcesInput ? preg_split('/\r\n|\r|\n/', $sourcesInput) : [];
        $sourcesArray = array_filter(array_map('trim', $sourcesArray));

        $socialMediaInput = $request->input('social_media', []);
        $socialMediaArray = array_filter($socialMediaInput);

        // --- Create Event ---
        $event = Event::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'startDate' => Carbon::parse($validated['startDate']), // Ensure Carbon instance
            'endDate' => isset($validated['endDate']) ? Carbon::parse($validated['endDate']) : null,
            'location' => $validated['location'],
            'is_virtual' => $request->boolean('is_virtual'), // Use boolean helper
            'eventType' => $validated['eventType'],
            'featured_image_url' => $validated['featured_image_url'],
            'featured_image_path' => $imagePath,
            'website_url' => $validated['website_url'],
            'country_id' => $validated['country_id'],
            'status' => 'pending',
            'created_by' => Auth::id(),
            'sources' => $sourcesArray,
            'social_media' => $socialMediaArray,
            'key_achievements' => $validated['key_achievements'] ?? null,
            // 'featured_article_url' => $validated['featured_article_url'] ?? null,
            // Slug generated by trait
        ]);

        // --- Handle Tags ---
        $this->syncTags($request->input('tags'), $event);

        // --- TODO: Handle relationships like sponsors if included in form ---

        $redirectRoute = Auth::user()->is_admin ? 'admin.pending' : 'my.submissions';
        return redirect()->route($redirectRoute)
            ->with('success', 'Event submitted successfully and awaiting approval!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Event $event): View // Use Route Model Binding
    {
        // Use policy check later: $this->authorize('view', $event);
        if ($event->status !== 'approved' && (!Auth::check() || !Auth::user()->is_admin)) {
            abort(404);
        }

        $sort = $request->input('sort', 'newest');

        $event->load(['country', 'tags', 'creator', 'likes']);
        // TODO: Load sponsors/organizers relationships when implemented

        // Fetch and sort comments separately
        $commentsQuery = $event->comments()
            ->with(['user', 'likes', 'replies' => function ($query) use ($sort) {
                $query->with(['user', 'likes'])->withCount('likes');
                if ($sort === 'likes') $query->orderByDesc('likes_count');
                $query->orderByDesc('created_at');
            }])
            ->withCount('likes');

        if ($sort === 'likes') $commentsQuery->orderByDesc('likes_count');
        else $commentsQuery->orderByDesc('created_at');

        $comments = $commentsQuery->get();


        return view('events.show', [
            'event' => $event,
            'comments' => $comments,
            'currentSort' => $sort,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event): View
    {
        $this->authorize('update', $event); // Use policy check later

        $event->load('tags');
        $countries = Country::orderBy('name')->pluck('name', 'id');
        $eventTypes = ['Awareness Campaign', 'Protest', 'Conference', 'Fundraiser', 'Workshop', 'Webinar', 'Other']; // Example

        return view('events.edit', [
            'event' => $event,
            'countries' => $countries,
            'eventTypes' => $eventTypes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('update', $event); // Use policy check later

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'startDate' => ['required', 'date'],
            'endDate' => ['nullable', 'date', 'after_or_equal:startDate'],
            'location' => ['nullable', 'string', 'max:500'],
            'is_virtual' => ['sometimes', 'boolean'],
            'eventType' => ['nullable', 'string', 'max:100'],
            'featured_image_url' => ['nullable', 'url', 'max:500'],
            'featured_image' => ['nullable', File::image()->max(2048)],
            'website_url' => ['nullable', 'url', 'max:500'],
            'country_id' => ['nullable', 'integer', 'exists:countries,id'],
            'tags' => ['nullable', 'string', 'max:500'],
            'social_media' => ['nullable', 'array'],
            'social_media.*' => ['nullable', 'url', 'max:500'],
            'sources' => ['nullable', 'string'],
            'key_achievements' => ['nullable', 'string', 'max:2000'],
            // 'featured_article_url' => ['nullable', 'url', 'max:500'],
        ], [
            'social_media.*.url' => 'Each social media link must be a valid URL.',
        ]);

        // --- Handle Image Upload ---
        if ($request->hasFile('featured_image') && $request->file('featured_image')->isValid()) {
            if ($event->featured_image_path && Storage::disk('public')->exists($event->featured_image_path)) {
                Storage::disk('public')->delete($event->featured_image_path);
            }
            $validated['featured_image_path'] = $request->file('featured_image')->store('event-images', 'public');
            $validated['featured_image_url'] = null;
        } else {
            if ($request->filled('featured_image_url') && $event->featured_image_path) {
                if (Storage::disk('public')->url($event->featured_image_path) !== $request->input('featured_image_url')) {
                    Storage::disk('public')->delete($event->featured_image_path);
                    $validated['featured_image_path'] = null;
                } else {
                    $validated['featured_image_path'] = $event->featured_image_path;
                }
            } else if (!$request->filled('featured_image_url')) {
                $validated['featured_image_path'] = $event->featured_image_path;
            }
            $validated['featured_image_url'] = $request->input('featured_image_url');
        }

        // --- Prepare JSON fields ---
        $sourcesInput = $request->input('sources');
        $sourcesArray = $sourcesInput ? preg_split('/\r\n|\r|\n/', $sourcesInput) : [];
        $sourcesArray = array_filter(array_map('trim', $sourcesArray));
        $validated['sources'] = $sourcesArray;

        $socialMediaInput = $request->input('social_media', []);
        $socialMediaArray = array_filter($socialMediaInput);
        $validated['social_media'] = $socialMediaArray;

        // Add fields before update
        $validated['key_achievements'] = $request->input('key_achievements');
        // $validated['featured_article_url'] = $request->input('featured_article_url');
        $validated['is_virtual'] = $request->boolean('is_virtual'); // Ensure boolean
        // Parse dates before update
        $validated['startDate'] = Carbon::parse($validated['startDate']);
        $validated['endDate'] = isset($validated['endDate']) ? Carbon::parse($validated['endDate']) : null;

        // --- Update Record ---
        $event->update($validated);

        // --- Handle Tags ---
        $this->syncTags($request->input('tags'), $event);

        // --- Determine Redirect ---
        $redirectRoute = Auth::user()->is_admin ? 'admin.pending' : 'my.submissions';
        if (Auth::user()->is_admin && Auth::user()->can('view', $event->fresh())) {
            $redirectRoute = 'events.show';
        }

        return redirect()->route($redirectRoute, $event)
            ->with('success', 'Event updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event): RedirectResponse
    {
        $this->authorize('delete', $event); // Use policy check later

        // Delete featured image file
        if ($event->featured_image_path && Storage::disk('public')->exists($event->featured_image_path)) {
            Storage::disk('public')->delete($event->featured_image_path);
        }

        $event->delete();

        $redirectRoute = Auth::user()->is_admin ? 'admin.pending' : 'my.submissions';
        return redirect()->route($redirectRoute)->with('success', 'Event deleted successfully.');
    }

    /**
     * Helper function to process and sync tags for an Event.
     */
    private function syncTags(?string $tagsInput, Event $event): void
    {
        if (is_null($tagsInput)) {
            $event->tags()->detach();
            return;
        }

        $tagNames = explode(',', $tagsInput);
        $tagIds = [];
        foreach ($tagNames as $tagName) {
            $trimmedName = trim($tagName);
            if ($trimmedName) {
                $tag = Tag::firstOrCreate(
                    ['slug' => Str::slug($trimmedName)],
                    ['name' => $trimmedName]
                );
                $tagIds[] = $tag->id;
            }
        }
        $event->tags()->sync($tagIds);
    }
}
