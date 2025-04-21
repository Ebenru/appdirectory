<?php

namespace App\Http\Controllers;

use App\Models\Organization; // Import Organization
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

class OrganizationController extends Controller
{
    use AuthorizesRequests; // Use Trait

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // Basic status filtering for public/admin view
        $query = Organization::query();
        if (!Auth::check() || !Auth::user()->is_admin) {
            $query->where('status', 'approved');
        }

        // TODO: Add search/filtering based on request inputs ($request->input('search'), $request->input('type'), etc.)

        $organizations = $query->with(['country', 'tags']) // Eager load common relations
            ->orderBy('name', 'asc')
            ->paginate(12)
            ->withQueryString();

        return view('organizations.index', [
            'organizations' => $organizations,
            // Pass filter options if implemented
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Basic authorization (any logged-in user can attempt to create)
        $this->authorize('create', Organization::class); // Use policy if defined, or just check auth

        $countries = Country::orderBy('name')->pluck('name', 'id');
        // TODO: Define Organization types/scopes in Model or config
        $organizationTypes = ['NGO', 'Government Body', 'International Org', 'Trade Association', 'Other']; // Example
        $organizationScopes = ['Local', 'National', 'International']; // Example

        return view('organizations.create', [
            'countries' => $countries,
            'organizationTypes' => $organizationTypes,
            'organizationScopes' => $organizationScopes,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Organization::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'type' => ['nullable', 'string', 'max:100'], // Add validation if using predefined list
            'foundingYear' => ['nullable', 'integer', 'min:1000', 'max:' . date('Y')],
            'scope' => ['nullable', 'string', 'max:50'],
            'logo_url' => ['nullable', 'url', 'max:500', Rule::requiredIf(!$request->hasFile('logo'))],
            'logo' => ['nullable', Rule::requiredIf(!$request->filled('logo_url')), File::image()->max(2048)],
            'website_url' => ['nullable', 'url', 'max:500'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'tags' => ['nullable', 'string', 'max:500'],
            'social_media' => ['nullable', 'array'],
            'social_media.*' => ['nullable', 'url', 'max:500'],
            'sources' => ['nullable', 'string'],
            'key_achievements' => ['nullable', 'string', 'max:2000'],
            'featured_article_url' => ['nullable', 'url', 'max:500'],
        ], [
            'logo_url.required_if' => 'Please provide a Logo URL or upload a logo file.',
            'logo.required_if' => 'Please upload a logo file or provide a Logo URL.',
            'social_media.*.url' => 'Each social media link must be a valid URL.',
        ]);

        // --- Handle Logo Upload ---
        $logoPath = null;
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $logoPath = $request->file('logo')->store('organization-logos', 'public');
            $validated['logo_url'] = null;
        }

        // --- Prepare JSON fields ---
        $sourcesInput = $request->input('sources');
        $sourcesArray = $sourcesInput ? preg_split('/\r\n|\r|\n/', $sourcesInput) : [];
        $sourcesArray = array_filter(array_map('trim', $sourcesArray));

        $socialMediaInput = $request->input('social_media', []);
        $socialMediaArray = array_filter($socialMediaInput);

        // --- Create Organization ---
        $organization = Organization::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'foundingYear' => $validated['foundingYear'],
            'scope' => $validated['scope'],
            'logo_url' => $validated['logo_url'],
            'logo_path' => $logoPath,
            'website_url' => $validated['website_url'],
            'country_id' => $validated['country_id'],
            'status' => 'pending', // Default to pending
            'created_by' => Auth::id(),
            'sources' => $sourcesArray,
            'social_media' => $socialMediaArray,
            'key_achievements' => $validated['key_achievements'] ?? null,
            'featured_article_url' => $validated['featured_article_url'] ?? null,
            // Slug generated by trait
        ]);

        // --- Handle Tags ---
        $this->syncTags($request->input('tags'), $organization);

        // Redirect to 'My Submissions' or admin pending list
        $redirectRoute = Auth::user()->is_admin ? 'admin.pending' : 'my.submissions';
        return redirect()->route($redirectRoute)
            ->with('success', 'Organization submitted successfully and awaiting approval!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Organization $organization): View // Use Route Model Binding
    {
        // Use policy check later: $this->authorize('view', $organization);
        if ($organization->status !== 'approved' && (!Auth::check() || !Auth::user()->is_admin)) {
            abort(404);
        }

        $sort = $request->input('sort', 'newest');

        $organization->load(['country', 'tags', 'creator', 'likes']);

        // Fetch and sort comments separately
        $commentsQuery = $organization->comments()
            ->with(['user', 'likes', 'replies' => function ($query) use ($sort) {
                $query->with(['user', 'likes'])->withCount('likes');
                if ($sort === 'likes') $query->orderByDesc('likes_count');
                $query->orderByDesc('created_at');
            }])
            ->withCount('likes');

        if ($sort === 'likes') $commentsQuery->orderByDesc('likes_count');
        else $commentsQuery->orderByDesc('created_at');

        $comments = $commentsQuery->get();

        return view('organizations.show', [
            'organization' => $organization,
            'comments' => $comments,
            'currentSort' => $sort,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization): View
    {
        $this->authorize('update', $organization); // Use policy check later

        $organization->load('tags');
        $countries = Country::orderBy('name')->pluck('name', 'id');
        $organizationTypes = ['NGO', 'Government Body', 'International Org', 'Trade Association', 'Other']; // Example
        $organizationScopes = ['Local', 'National', 'International']; // Example

        return view('organizations.edit', [
            'organization' => $organization,
            'countries' => $countries,
            'organizationTypes' => $organizationTypes,
            'organizationScopes' => $organizationScopes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organization $organization): RedirectResponse
    {
        $this->authorize('update', $organization); // Use policy check later

        // Validation similar to store, make fields nullable for update
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'type' => ['nullable', 'string', 'max:100'],
            'foundingYear' => ['nullable', 'integer', 'min:1000', 'max:' . date('Y')],
            'scope' => ['nullable', 'string', 'max:50'],
            'logo_url' => ['nullable', 'url', 'max:500'],
            'logo' => ['nullable', File::image()->max(2048)],
            'website_url' => ['nullable', 'url', 'max:500'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'tags' => ['nullable', 'string', 'max:500'],
            'social_media' => ['nullable', 'array'],
            'social_media.*' => ['nullable', 'url', 'max:500'],
            'sources' => ['nullable', 'string'],
            'key_achievements' => ['nullable', 'string', 'max:2000'],
            'featured_article_url' => ['nullable', 'url', 'max:500'],
            // Add status update validation if admins can change it via edit form
            // 'status' => ['sometimes', 'required', Rule::in(['draft', 'pending', 'approved', 'rejected', 'archived'])],
        ], [
            'social_media.*.url' => 'Each social media link must be a valid URL.',
        ]);

        // --- Handle Logo Upload ---
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            if ($organization->logo_path && Storage::disk('public')->exists($organization->logo_path)) {
                Storage::disk('public')->delete($organization->logo_path);
            }
            $validated['logo_path'] = $request->file('logo')->store('organization-logos', 'public');
            $validated['logo_url'] = null;
        } else {
            if ($request->filled('logo_url') && $organization->logo_path) {
                if (Storage::disk('public')->url($organization->logo_path) !== $request->input('logo_url')) {
                    Storage::disk('public')->delete($organization->logo_path);
                    $validated['logo_path'] = null;
                } else {
                    $validated['logo_path'] = $organization->logo_path;
                }
            } else if (!$request->filled('logo_url')) {
                $validated['logo_path'] = $organization->logo_path;
            }
            // Always update URL field based on input
            $validated['logo_url'] = $request->input('logo_url');
        }

        // --- Prepare JSON fields ---
        $sourcesInput = $request->input('sources');
        $sourcesArray = $sourcesInput ? preg_split('/\r\n|\r|\n/', $sourcesInput) : [];
        $sourcesArray = array_filter(array_map('trim', $sourcesArray));
        $validated['sources'] = $sourcesArray;

        $socialMediaInput = $request->input('social_media', []);
        $socialMediaArray = array_filter($socialMediaInput);
        $validated['social_media'] = $socialMediaArray;

        // Add fields explicitly before update if not directly validated
        $validated['key_achievements'] = $request->input('key_achievements');
        $validated['featured_article_url'] = $request->input('featured_article_url');
        // Only update status if it was part of the validated data (admin form)
        // if(!isset($validated['status'])) unset($validated['status']);


        // --- Update Record ---
        $organization->update($validated);

        // --- Handle Tags ---
        $this->syncTags($request->input('tags'), $organization);

        // --- Determine Redirect ---
        $redirectRoute = Auth::user()->is_admin ? 'admin.pending' : 'my.submissions'; // Default redirect
        // Admins might prefer detail page if it's viewable
        if (Auth::user()->is_admin && Auth::user()->can('view', $organization->fresh())) {
            $redirectRoute = 'organizations.show';
        }

        return redirect()->route($redirectRoute, $organization)
            ->with('success', 'Organization updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization): RedirectResponse
    {
        $this->authorize('delete', $organization); // Use policy check later

        // Delete logo file
        if ($organization->logo_path && Storage::disk('public')->exists($organization->logo_path)) {
            Storage::disk('public')->delete($organization->logo_path);
        }

        $organization->delete();

        $redirectRoute = Auth::user()->is_admin ? 'admin.pending' : 'my.submissions';
        return redirect()->route($redirectRoute)->with('success', 'Organization deleted successfully.');
    }

    /**
     * Helper function to process and sync tags for an Organization.
     */
    private function syncTags(?string $tagsInput, Organization $organization): void
    {
        if (is_null($tagsInput)) {
            $organization->tags()->detach();
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
        $organization->tags()->sync($tagIds);
    }
}
