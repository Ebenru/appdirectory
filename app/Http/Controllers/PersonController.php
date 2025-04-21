<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Country;
use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // <--- IMPORT THE TRAIT NAMESPACE
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\View\View;

class PersonController extends Controller
{
    // --- ADD THE TRAIT HERE ---
    use AuthorizesRequests;
    // --------------------------

    /**
     * Display a listing of approved people.
     */
    public function index(Request $request): View
    {
        $query = Person::where('status', 'approved');

        // --- Category Filtering ---
        $selectedCategory = $request->input('category');
        if ($selectedCategory && array_key_exists($selectedCategory, Person::CATEGORIES)) {
            $query->where('pplCategory', $selectedCategory);
        }

        // --- Search Filtering ---
        $searchQuery = $request->input('search');
        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('fullName', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('title', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('description', 'LIKE', "%{$searchQuery}%");
            });
        }

        // --- Eager load relationships needed ---
        $people = $query->with(['country', 'tags'])
            ->orderBy('rank', 'asc')
            ->orderBy('fullName', 'asc')
            ->paginate(12)
            ->withQueryString();

        return view('people.index', [
            'people' => $people,
            'categories' => Person::CATEGORIES,
            'selectedCategory' => $selectedCategory,
            'searchQuery' => $searchQuery,
        ]);
    }

    /**
     * Show the form for creating a new person.
     */
    public function create(): View
    {
        $countries = Country::orderBy('name')->pluck('name', 'id');

        return view('people.create', [
            'categories' => Person::CATEGORIES,
            'countries' => $countries,
        ]);
    }

    /**
     * Store a newly created person in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Note: No direct authorization needed here, handled by 'auth' middleware on route

        $validated = $request->validate([
            'fullName' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'pplCategory' => ['required', 'string', Rule::in(array_keys(Person::CATEGORIES))],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'nationalities' => ['nullable', 'array'],
            'nationalities.*' => ['integer', 'exists:countries,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'picture_url' => ['nullable', 'url', 'max:500', Rule::requiredIf(!$request->hasFile('photo'))],
            'photo' => ['nullable', Rule::requiredIf(!$request->filled('picture_url')), File::image()->max(2048)],
            'tags' => ['nullable', 'string', 'max:500'],
            'social_media' => ['nullable', 'array'],
            'social_media.*' => ['nullable', 'url', 'max:500'],
            'sources' => ['nullable', 'string'],
            'key_achievements' => ['nullable', 'string', 'max:2000'],
            'featured_article_url' => ['nullable', 'url', 'max:500'],
        ], [
            'picture_url.required_if' => 'Please provide a Picture URL or upload a photo.',
            'photo.required_if' => 'Please upload a photo or provide a Picture URL.',
            'social_media.*.url' => 'Each social media link must be a valid URL.',
            'featured_article_url.url' => 'The featured article link must be a valid URL.',
            'nationalities.*.exists' => 'One or more selected nationalities are invalid.',
        ]);

        // --- Handle Photo Upload ---
        $photoPath = null;
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $photoPath = $request->file('photo')->store('person-photos', 'public');
            $validated['picture_url'] = null;
        }

        // --- Prepare JSON fields ---
        $sourcesInput = $request->input('sources');
        $sourcesArray = $sourcesInput ? preg_split('/\r\n|\r|\n/', $sourcesInput) : [];
        $sourcesArray = array_filter(array_map('trim', $sourcesArray));

        $socialMediaInput = $request->input('social_media', []);
        $socialMediaArray = array_filter($socialMediaInput);

        // --- Create Person ---
        $person = Person::create([
            'fullName' => $validated['fullName'],
            'title' => $validated['title'],
            'pplCategory' => $validated['pplCategory'],
            'country_id' => $validated['country_id'],
            'description' => $validated['description'],
            'picture_url' => $validated['picture_url'],
            'photo_path' => $photoPath,
            'sources' => $sourcesArray,
            'social_media' => $socialMediaArray,
            'key_achievements' => $validated['key_achievements'] ?? null,
            'featured_article_url' => $validated['featured_article_url'] ?? null,
            'status' => 'pending',
            'submitted_by_id' => Auth::id(),
            'rank' => 0,
        ]);

        // --- Handle Tags ---
        $this->syncTags($request->input('tags'), $person);

        // --- Handle Nationalities (Sync after person is created) ---
        $nationalityIds = $request->input('nationalities', []); // Get array, default to empty
        $person->nationalities()->sync($nationalityIds); // <-- Sync the relationship

        return redirect()->route('my.submissions')
            ->with('success', 'Submission received and awaiting approval!');
    }

    /**
     * Display the specified person.
     */
    public function show(Request $request, Person $person): View
    {
        // Use the 'view' policy method via authorize() from the trait
        $this->authorize('view', $person); // <-- Now uses the trait

        $sort = $request->input('sort', 'newest');

        $person->load(['country', 'nationalities', 'tags', 'submittedBy', 'approvedBy', 'likes']);

        $commentsQuery = $person->comments()
            ->with([
                'user',
                'likes',
                'replies' => function ($query) use ($sort) {
                    $query->with(['user', 'likes'])->withCount('likes');
                    if ($sort === 'likes') $query->orderByDesc('likes_count');
                    $query->orderByDesc('created_at');
                }
            ])
            ->withCount('likes');

        if ($sort === 'likes') $commentsQuery->orderByDesc('likes_count');
        else $commentsQuery->orderByDesc('created_at');

        $comments = $commentsQuery->get();

        return view('people.show', [
            'person' => $person,
            'comments' => $comments,
            'currentSort' => $sort,
        ]);
    }

    /**
     * Show the form for editing the specified person.
     */
    public function edit(Person $person): View
    {
        // Use the 'update' policy method via authorize() from the trait
        $this->authorize('update', $person); // <-- Now uses the trait

        $person->load('tags');
        $countries = Country::orderBy('name')->pluck('name', 'id');

        return view('people.edit', [
            'person' => $person,
            'categories' => Person::CATEGORIES,
            'countries' => $countries,
        ]);
    }

    /**
     * Update the specified person in storage.
     */
    public function update(Request $request, Person $person): RedirectResponse
    {
        // Use the 'update' policy method via authorize() from the trait
        $this->authorize('update', $person); // <-- Now uses the trait

        $validated = $request->validate([
            'fullName' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'pplCategory' => ['required', 'string', Rule::in(array_keys(Person::CATEGORIES))],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'nationalities' => ['nullable', 'array'], // <-- ADD validation
            'nationalities.*' => ['integer', 'exists:countries,id'], // <-- ADD validation
            'description' => ['nullable', 'string', 'max:5000'],
            'picture_url' => ['nullable', 'url', 'max:500'],
            'photo' => ['nullable', File::image()->max(2048)],
            'tags' => ['nullable', 'string', 'max:500'],
            'social_media' => ['nullable', 'array'],
            'social_media.*' => ['nullable', 'url', 'max:500'],
            'sources' => ['nullable', 'string'],
            'key_achievements' => ['nullable', 'string', 'max:2000'],
            'featured_article_url' => ['nullable', 'url', 'max:500'],
        ], [
            'social_media.*.url' => 'Each social media link must be a valid URL.',
            'featured_article_url.url' => 'The featured article link must be a valid URL.',
            'nationalities.*.exists' => 'One or more selected nationalities are invalid.',
        ]);

        // --- Handle Photo Upload ---
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            if ($person->photo_path && Storage::disk('public')->exists($person->photo_path)) {
                Storage::disk('public')->delete($person->photo_path);
            }
            $validated['photo_path'] = $request->file('photo')->store('person-photos', 'public');
            $validated['picture_url'] = null;
        } else {
            if ($request->filled('picture_url') && $person->photo_path) {
                if (Storage::disk('public')->url($person->photo_path) !== $request->input('picture_url')) {
                    Storage::disk('public')->delete($person->photo_path);
                    $validated['photo_path'] = null;
                } else {
                    $validated['photo_path'] = $person->photo_path;
                }
            } else if (!$request->filled('picture_url')) {
                $validated['photo_path'] = $person->photo_path;
            }
            // Need to make sure picture_url is included if not cleared
            if (!isset($validated['picture_url'])) {
                $validated['picture_url'] = $request->input('picture_url');
            }
        }

        // --- Prepare JSON fields ---
        $sourcesInput = $request->input('sources');
        $sourcesArray = $sourcesInput ? preg_split('/\r\n|\r|\n/', $sourcesInput) : [];
        $sourcesArray = array_filter(array_map('trim', $sourcesArray));
        $validated['sources'] = $sourcesArray;


        $socialMediaInput = $request->input('social_media', []);
        $socialMediaArray = array_filter($socialMediaInput);
        $validated['social_media'] = $socialMediaArray;
        $validated['key_achievements'] = $request->input('key_achievements');
        $validated['featured_article_url'] = $request->input('featured_article_url');

        // --- Update the person record ---
        $person->update($validated);

        // --- Handle Tags ---
        $this->syncTags($request->input('tags'), $person);

        // --- Handle Nationalities (Sync after person is updated) ---
        $nationalityIds = $request->input('nationalities', []);
        $person->nationalities()->sync($nationalityIds);

        // --- Determine Redirect ---
        $redirectRoute = 'my.submissions';
        if (Auth::user()->is_admin) {
            // Use can() method provided by AuthorizesRequests trait
            if (Auth::user()->can('view', $person)) {
                $redirectRoute = 'people.show';
            } else {
                $redirectRoute = 'admin.pending';
            }
        }

        return redirect()->route($redirectRoute, $person) // Pass $person for show route
            ->with('success', 'Contributor updated successfully!');
    }

    /**
     * Remove the specified person from storage.
     */
    public function destroy(Person $person): RedirectResponse
    {
        // Use the 'delete' policy method via authorize() from the trait
        $this->authorize('delete', $person); // <-- Now uses the trait

        // --- Delete associated photo file ---
        if ($person->photo_path && Storage::disk('public')->exists($person->photo_path)) {
            Storage::disk('public')->delete($person->photo_path);
        }

        // --- Delete the database record ---
        $person->delete();

        // --- Determine Redirect ---
        $redirectRoute = 'my.submissions';
        if (Auth::user()->is_admin) {
            $redirectRoute = 'admin.pending';
        }

        return redirect()->route($redirectRoute)->with('success', 'Submission deleted successfully.');
    }

    /**
     * Helper function to process and sync tags.
     * ... (syncTags method remains the same) ...
     */
    private function syncTags(?string $tagsInput, Person $person): void
    {
        if (is_null($tagsInput)) {
            $person->tags()->detach();
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
        $person->tags()->sync($tagIds);
    }
}
