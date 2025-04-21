<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Submit an Organization for the Directory
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Organization Details</x-ui.card-title>
                <x-ui.card-description>
                    Tell us about an organization that significantly adhered to our campaign. Your submission will be reviewed before publishing.
                </x-ui.card-description>
            </x-ui.card-header>
            <x-ui.card-content>
                <form method="POST" action="{{ route('companies.store') }}" class="space-y-6">
                    @csrf

                    {{-- Legal Name --}}
                    <div>
                        <x-ui.label for="legalName">Legal Name</x-ui.label>
                        <x-ui.input id="legalName" name="legalName" type="text" class="mt-1 block w-full" :value="old('legalName')" required autofocus />
                        <x-input-error :messages="$errors->get('legalName')" class="mt-2" />
                    </div>

                    {{-- Display Name (Optional) --}}
                    <div>
                        <x-ui.label for="displayName">Display Name (Optional)</x-ui.label>
                        <x-ui.input id="displayName" name="displayName" type="text" class="mt-1 block w-full" :value="old('displayName')" />
                        <x-input-error :messages="$errors->get('displayName')" class="mt-2" />
                    </div>

                    {{-- Category --}}
                    <div>
                        <x-ui.label for="cmpCategory">Category</x-ui.label>
                        <x-ui.select id="cmpCategory" name="cmpCategory" class="mt-1 block w-full" required>
                            <option value="" disabled selected>Select a category...</option>
                            @foreach($categories as $slug => $name)
                            <option value="{{ $slug }}" @selected(old('cmpCategory')==$slug)>
                                {{ $name }}
                            </option>
                            @endforeach
                        </x-ui.select>
                        <x-input-error :messages="$errors->get('cmpCategory')" class="mt-2" />
                    </div>

                    {{-- === Parent Group (Optional) === --}}
                    <div>
                        <x-ui.label for="group_id">Parent Group (Optional)</x-ui.label>
                        <x-ui.select id="group_id" name="group_id" class="mt-1 block w-full">
                            <option value="">None (Independent Company)</option> {{-- Allow selecting no group --}}
                            @foreach($groups as $group_id => $group_name) {{-- Use key=>value from pluck --}}
                            {{-- Pre-select in edit view --}}
                            <option value="{{ $group_id }}" @selected(old('group_id', $company->group_id ?? null) == $group_id)>
                                {{ $group_name }}
                            </option>
                            @endforeach
                        </x-ui.select>
                        <x-input-error :messages="$errors->get('group_id')" class="mt-2" />
                    </div>

                    {{-- === NEW: Country (e.g., HQ) === --}}
                    <div>
                        <x-ui.label for="country_id">Primary Country (HQ)</x-ui.label>
                        <x-ui.select id="country_id" name="country_id" class="mt-1 block w-full" required>
                            <option value="" disabled selected>Select primary country...</option>
                            {{-- Corrected Loop for Plucked Array --}}
                            @foreach($countries as $country_id => $country_name) {{-- Loop key=>value --}}
                            <option value="{{ $country_id }}" @selected(old('country_id')==$country_id)> {{-- Use $country_id for value --}}
                                {{ $country_name }} {{-- Use $country_name for display --}}
                            </option>
                            @endforeach
                            {{-- End Corrected Loop --}}
                        </x-ui.select>
                        <x-input-error :messages="$errors->get('country_id')" class="mt-2" />
                    </div>

                    {{-- Description --}}
                    <div>
                        <x-ui.label for="description">How did they contribute?</x-ui.label>
                        <x-ui.textarea id="description" name="description" rows="4" class="mt-1 block w-full">{{ old('description') }}</x-ui.textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    {{-- Logo URL (Consider Image Upload later) --}}
                    <div>
                        <x-ui.label for="logo_url">Logo URL (Optional)</x-ui.label>
                        <x-ui.input id="logo_url" name="logo_url" type="url" class="mt-1 block w-full" placeholder="https://..." :value="old('logo_url')" />
                        <p class="mt-1 text-xs text-muted-foreground">Link to a logo hosted online.</p>
                        <x-input-error :messages="$errors->get('logo_url')" class="mt-2" />
                    </div>

                    {{-- Website URL (Optional) --}}
                    <div>
                        <x-ui.label for="website_url">Website URL (Optional)</x-ui.label>
                        <x-ui.input id="website_url" name="website_url" type="url" class="mt-1 block w-full" placeholder="https://..." :value="old('website_url')" />
                        <x-input-error :messages="$errors->get('website_url')" class="mt-2" />
                    </div>
                    {{-- === NEW: Tags === --}}
                    <div>
                        <x-ui.label for="tags">Tags (Comma-separated)</x-ui.label>
                        <x-ui.input id="tags" name="tags" type="text" class="mt-1 block w-full" placeholder="e.g., consumer-goods, environment, manufacturing" :value="old('tags')" />
                        <p class="mt-1 text-xs text-muted-foreground">Categorize the organization with relevant keywords.</p>
                        <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                    </div>

                    {{-- === NEW: Social Media === --}}
                    <div class="space-y-2 rounded-md border dark:border-gray-700 p-4">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Social Media Links (Optional)</h4>
                        <div class="flex items-center gap-2">
                            <x-lucide-twitter class="w-5 h-5 text-muted-foreground" />
                            <x-ui.input name="social_media[twitter]" type="url" class="block w-full text-sm" placeholder="Twitter URL" :value="old('social_media.twitter')" />
                        </div>
                        <div class="flex items-center gap-2">
                            <x-lucide-linkedin class="w-5 h-5 text-muted-foreground" />
                            <x-ui.input name="social_media[linkedin]" type="url" class="block w-full text-sm" placeholder="LinkedIn URL" :value="old('social_media.linkedin')" />
                        </div>
                        <x-input-error :messages="$errors->get('social_media.*')" class="mt-2 text-xs" />
                    </div>

                    {{-- === NEW: Sources === --}}
                    <div>
                        <x-ui.label for="sources">Sources (Optional, one URL per line)</x-ui.label>
                        <x-ui.textarea id="sources" name="sources" rows="3" class="mt-1 block w-full" placeholder="Verification links...">{{ old('sources') }}</x-ui.textarea>
                        <x-input-error :messages="$errors->get('sources')" class="mt-2" />
                        <x-input-error :messages="$errors->get('sources.*')" class="mt-2 text-xs" />
                    </div>

                    {{-- === NEW: Key Achievements === --}}
                    <div class="mt-4">
                        <x-ui.label for="key_achievements">Key Achievements / Contribution Summary (Optional)</x-ui.label>
                        <x-ui.textarea id="key_achievements" name="key_achievements" rows="3" class="mt-1 block w-full" placeholder="List 2-3 key points related to the organization's impact...">{{ old('key_achievements') }}</x-ui.textarea>
                        <p class="mt-1 text-xs text-muted-foreground">Highlight specific accomplishments or contributions.</p>
                        <x-input-error :messages="$errors->get('key_achievements')" class="mt-2" />
                    </div>

                    {{-- === NEW: Featured Article URL === --}}
                    <div class="mt-4">
                        <x-ui.label for="featured_article_url">Featured Article/Link (Optional)</x-ui.label>
                        <x-ui.input id="featured_article_url" name="featured_article_url" type="url" class="mt-1 block w-full" placeholder="https://example.com/company-feature" :value="old('featured_article_url')" />
                        <p class="mt-1 text-xs text-muted-foreground">Link to a primary news article or resource about the organization.</p>
                        <x-input-error :messages="$errors->get('featured_article_url')" class="mt-2" />
                    </div>

                    {{-- Submit Button --}}

                    <div class="flex items-center justify-end pt-4">
                        <x-ui.button type="submit">
                            Submit for Review
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card-content>
        </x-ui.card>
    </div>
</x-app-layout>