<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Country; // Import Country
use App\Models\Group;
use App\Models\Tag;     // Import Tag
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // <-- IMPORT TRAIT NAMESPACE
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\View\View;

class CompanyController extends Controller
{
    // --- ADD THE TRAIT HERE ---
    use AuthorizesRequests;
    // --------------------------

    /**
     * Display a listing of approved companies.
     */
    public function index(Request $request): View // Return type hint
    {
        $query = Company::where('status', 'approved');

        // --- Category Filtering ---
        $selectedCategory = $request->input('category');
        if ($selectedCategory && array_key_exists($selectedCategory, Company::CATEGORIES)) {
            $query->where('cmpCategory', $selectedCategory);
        }

        // --- Search Filtering ---
        $searchQuery = $request->input('search');
        if ($searchQuery) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('legalName', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('displayName', 'LIKE', "%{$searchQuery}%")
                    ->orWhere('description', 'LIKE', "%{$searchQuery}%");
            });
        }

        // --- Eager load & Pagination & Ordering ---
        $companies = $query->with(['country', 'tags']) // Eager load Country and Tags
            ->orderBy('rank', 'asc')
            ->orderByRaw('ISNULL(displayName), displayName ASC') // Handle sorting with optional displayName
            ->orderBy('legalName', 'asc')
            ->paginate(12)
            ->withQueryString();

        return view('companies.index', [
            'companies' => $companies,
            'categories' => Company::CATEGORIES,
            'selectedCategory' => $selectedCategory,
            'searchQuery' => $searchQuery,
        ]);
    }

    /**
     * Show the form for creating a new company.
     */
    public function create(): View // Return type hint
    {
        $countries = Country::orderBy('name')->pluck('name', 'id'); // Use pluck
        $groups = Group::orderBy('name')->pluck('name', 'id');

        return view('companies.create', [
            'categories' => Company::CATEGORIES,
            'countries' => $countries,
            'groups' => $groups,
        ]);
    }

    /**
     * Store a newly created company in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'legalName' => ['required', 'string', 'max:255'],
            'displayName' => ['nullable', 'string', 'max:255'],
            'cmpCategory' => ['required', 'string', Rule::in(array_keys(Company::CATEGORIES))],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'group_id' => ['nullable', 'integer', 'exists:groups,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'website_url' => ['nullable', 'url', 'max:500'],
            'logo_url' => ['nullable', 'url', 'max:500', Rule::requiredIf(!$request->hasFile('logo'))],
            'logo' => ['nullable', Rule::requiredIf(!$request->filled('logo_url')), File::image()->max(2048)],
            'tags' => ['nullable', 'string', 'max:500'],
            'social_media' => ['nullable', 'array'],
            'social_media.*' => ['nullable', 'url', 'max:500'],
            'sources' => ['nullable', 'string'],
            // 'sources.*' => ['nullable', 'url', 'max:500'], // Optional: validate after split
            'key_achievements' => ['nullable', 'string', 'max:2000'], // Limit length
            'featured_article_url' => ['nullable', 'url', 'max:500'],
        ], [
            'logo_url.required_if' => 'Please provide a Logo URL or upload a logo file.',
            'logo.required_if' => 'Please upload a logo file or provide a Logo URL.',
            'social_media.*.url' => 'Each social media link must be a valid URL.',
            'featured_article_url.url' => 'The featured article link must be a valid URL.',
        ]);

        // --- Handle Logo Upload ---
        $logoPath = null;
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $logoPath = $request->file('logo')->store('company-logos', 'public');
            $validated['logo_url'] = null; // Prioritize uploaded logo
        }

        // --- Prepare JSON fields ---
        $sourcesInput = $request->input('sources');
        $sourcesArray = $sourcesInput ? preg_split('/\r\n|\r|\n/', $sourcesInput) : [];
        $sourcesArray = array_filter(array_map('trim', $sourcesArray));

        $socialMediaInput = $request->input('social_media', []);
        $socialMediaArray = array_filter($socialMediaInput);

        // --- Create Company ---
        $company = Company::create([
            'legalName' => $validated['legalName'],
            'displayName' => $validated['displayName'],
            'cmpCategory' => $validated['cmpCategory'],
            'country_id' => $validated['country_id'],
            'description' => $validated['description'],
            'website_url' => $validated['website_url'],
            'logo_url' => $validated['logo_url'],
            'logo_path' => $logoPath,
            'sources' => $sourcesArray,
            'social_media' => $socialMediaArray,
            'key_achievements' => $validated['key_achievements'] ?? null,
            'featured_article_url' => $validated['featured_article_url'] ?? null,
            'status' => 'pending',
            'submitted_by_id' => Auth::id(),
            'rank' => 0,
            // Slug generated by trait
        ]);

        // --- Handle Tags ---
        $this->syncTags($request->input('tags'), $company);

        return redirect()->route('my.submissions') // Redirect to my submissions
            ->with('success', 'Organization submission received and awaiting approval!');
    }

    /**
     * Display the specified company.
     */
    public function show(Request $request, Company $company): View // Return type hint
    {
        // Authorize using the 'view' policy method
        $this->authorize('view', $company); // <-- Use authorize()

        $sort = $request->input('sort', 'newest');

        // Eager load relationships
        $company->load(['country', 'tags', 'submittedBy', 'approvedBy', 'likes']); // Add 'group' later if needed

        // Fetch and sort comments
        $commentsQuery = $company->comments()
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

        return view('companies.show', [
            'company' => $company,
            'comments' => $comments,
            'currentSort' => $sort,
        ]);
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit(Company $company): View // Return type hint
    {
        // Authorize using the 'update' policy method
        $this->authorize('update', $company); // <-- Use authorize()

        // Eager load tags for the form
        $company->load('tags');
        $countries = Country::orderBy('name')->pluck('name', 'id');
        $groups = Group::orderBy('name')->pluck('name', 'id');

        return view('companies.edit', [
            'company' => $company,
            'categories' => Company::CATEGORIES,
            'countries' => $countries,
            'groups' => $groups,
        ]);
    }

    /**
     * Update the specified company in storage.
     */
    public function update(Request $request, Company $company): RedirectResponse
    {
        // Authorize using the 'update' policy method
        $this->authorize('update', $company); // <-- Use authorize()

        // Corrected validation: use 'logo' for file, 'displayName' casing
        $validated = $request->validate([
            'legalName' => ['required', 'string', 'max:255'],
            'displayName' => ['nullable', 'string', 'max:255'], // Correct casing
            'cmpCategory' => ['required', 'string', Rule::in(array_keys(Company::CATEGORIES))],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'group_id' => ['nullable', 'integer', 'exists:groups,id'],
            // 'group_id' => ['nullable', 'integer', 'exists:groups,id'], // Uncomment if needed
            'description' => ['nullable', 'string', 'max:5000'],
            'website_url' => ['nullable', 'url', 'max:500'],
            'logo_url' => ['nullable', 'url', 'max:500'],
            'logo' => ['nullable', File::image()->max(2048)], // Use 'logo' for file
            'tags' => ['nullable', 'string', 'max:500'],
            'social_media' => ['nullable', 'array'],
            'social_media.*' => ['nullable', 'url', 'max:500'],
            'sources' => ['nullable', 'string'],
            'key_achievements' => ['nullable', 'string', 'max:2000'],
            'featured_article_url' => ['nullable', 'url', 'max:500'],
        ], [
            'social_media.*.url' => 'Each social media link must be a valid URL.',
            'featured_article_url.url' => 'The featured article link must be a valid URL.',
        ]);

        // --- Handle Logo Upload ---
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) { // Check for 'logo'
            // Delete old logo file
            if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
                Storage::disk('public')->delete($company->logo_path);
            }
            // Store new logo
            $validated['logo_path'] = $request->file('logo')->store('company-logos', 'public');
            $validated['logo_url'] = null; // Prioritize uploaded file
        } else {
            // Handle clearing logo path if URL is now provided
            if ($request->filled('logo_url') && $company->logo_path) {
                if (Storage::disk('public')->url($company->logo_path) !== $request->input('logo_url')) {
                    Storage::disk('public')->delete($company->logo_path);
                    $validated['logo_path'] = null;
                } else {
                    $validated['logo_path'] = $company->logo_path;
                }
            } else if (!$request->filled('logo_url')) {
                $validated['logo_path'] = $company->logo_path; // Keep existing path
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
        $validated['key_achievements'] = $request->input('key_achievements');
        $validated['featured_article_url'] = $request->input('featured_article_url');

        // --- Update the company record ---
        $company->update($validated);

        // --- Handle Tags ---
        $this->syncTags($request->input('tags'), $company);

        // --- Determine Redirect ---
        $redirectRoute = 'my.submissions';
        if (Auth::user()->is_admin) {
            // Use can() method from trait
            if (Auth::user()->can('view', $company)) {
                $redirectRoute = 'companies.show';
            } else {
                $redirectRoute = 'admin.pending';
            }
        }

        return redirect()->route($redirectRoute, $company) // Pass $company for show route
            ->with('success', 'Organization updated successfully!');
    }

    /**
     * Remove the specified company from storage.
     */
    public function destroy(Company $company): RedirectResponse
    {
        // Authorize using the 'delete' policy method
        $this->authorize('delete', $company); // <-- Use authorize()

        // --- Delete associated logo file ---
        if ($company->logo_path && Storage::disk('public')->exists($company->logo_path)) {
            Storage::disk('public')->delete($company->logo_path);
        }

        // --- Delete the database record ---
        $company->delete();

        // --- Determine Redirect ---
        $redirectRoute = 'my.submissions';
        if (Auth::user()->is_admin) {
            $redirectRoute = 'admin.pending';
        }

        return redirect()->route($redirectRoute)->with('success', 'Organization deleted successfully.');
    }

    /**
     * Helper function to process and sync tags for a Company.
     *
     * @param string|null $tagsInput Comma-separated string of tag names.
     * @param Company $company The model to sync tags for.
     */
    private function syncTags(?string $tagsInput, Company $company): void // Type hint Company
    {
        if (is_null($tagsInput)) {
            $company->tags()->detach();
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
        $company->tags()->sync($tagIds);
    }
}
