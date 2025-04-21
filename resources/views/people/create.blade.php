<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Submit a Person for the Directory
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto py-12 sm:px-6 lg:px-8"> {{-- Constrain form width --}}
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Person Details</x-ui.card-title>
                <x-ui.card-description>
                    Tell us about someone who significantly adhered to our campaign. Your submission will be reviewed before publishing.
                </x-ui.card-description>
            </x-ui.card-header>

            <x-ui.card-content>
                <form method="POST" action="{{ route('people.store') }}" class="space-y-6" enctype="multipart/form-data">
                    @csrf {{-- CSRF Protection --}}

                    {{-- Full Name --}}
                    <div>
                        <x-ui.label for="fullName">Full Name</x-ui.label>
                        <x-ui.input id="fullName" name="fullName" type="text" class="mt-1 block w-full" :value="old('fullName')" required autofocus />
                        <x-input-error :messages="$errors->get('fullName')" class="mt-2" /> {{-- Assumes Breeze error component --}}
                    </div>

                    {{-- Title --}}
                    <div>
                        <x-ui.label for="title">Title / Role</x-ui.label>
                        <x-ui.input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    {{-- Category --}}
                    <div>
                        <x-ui.label for="pplCategory">Category</x-ui.label>
                        <x-ui.select id="pplCategory" name="pplCategory" class="mt-1 block w-full" required>
                            <option value="" disabled selected>Select a category...</option>
                            @foreach($categories as $slug => $name)
                            <option value="{{ $slug }}" @selected(old('pplCategory')==$slug)>
                                {{ $name }}
                            </option>
                            @endforeach
                        </x-ui.select>
                        <x-input-error :messages="$errors->get('pplCategory')" class="mt-2" />
                    </div>

                    {{-- === NEW: Country === --}}
                    <div>
                        <x-ui.label for="country_id">Primary Country</x-ui.label>
                        {{-- Fetch countries in controller and pass as $countries --}}
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

                    {{-- === Nationalities (Optional, Multiple) === --}}
                    <div>
                        <x-ui.label for="nationalities">Nationality / Citizenship (Optional)</x-ui.label>
                        {{-- Use a multi-select input. Add name="nationalities[]" for array submission --}}
                        <x-ui.select id="nationalities" name="nationalities[]" class="mt-1 block w-full h-32" multiple> {{-- Add multiple attribute and adjust height --}}
                            {{-- $countries passed from controller --}}
                            @foreach($countries as $country_id => $country_name)
                            {{-- Pre-select multiple values in edit view --}}
                            {{-- Check if old input exists OR if the person model has the nationality --}}
                            <option value="{{ $country_id }}"
                                @if(is_array(old('nationalities')) && in_array($country_id, old('nationalities')))
                                selected
                                @elseif(!is_array(old('nationalities')) && isset($person) && $person->nationalities->contains($country_id))
                                selected
                                @endif
                                >
                                {{ $country_name }}
                            </option>
                            @endforeach
                        </x-ui.select>
                        <p class="mt-1 text-xs text-muted-foreground">Hold Ctrl/Cmd to select multiple.</p>
                        <x-input-error :messages="$errors->get('nationalities')" class="mt-2" />
                        <x-input-error :messages="$errors->get('nationalities.*')" class="mt-2" /> {{-- Error for specific items --}}
                    </div>

                    {{-- Description --}}
                    <div>
                        <x-ui.label for="description">How did they contribute?</x-ui.label>
                        <x-ui.textarea id="description" name="description" rows="4" class="mt-1 block w-full">{{ old('description') }}</x-ui.textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    {{-- Picture URL (Consider Image Upload later) --}}
                    <div>
                        <x-ui.label for="picture_url">Picture URL (Optional)</x-ui.label>
                        <x-ui.input id="picture_url" name="picture_url" type="url" class="mt-1 block w-full" placeholder="https://..." :value="old('picture_url')" />
                        <p class="mt-1 text-xs text-muted-foreground">Link to an image hosted online (e.g., LinkedIn, website).</p>
                        <x-input-error :messages="$errors->get('picture_url')" class="mt-2" />
                    </div>

                    {{-- === NEW: Tags === --}}
                    <div>
                        <x-ui.label for="tags">Tags (Comma-separated)</x-ui.label>
                        <x-ui.input id="tags" name="tags" type="text" class="mt-1 block w-full" placeholder="e.g., human-rights, tech, activism" :value="old('tags')" />
                        <p class="mt-1 text-xs text-muted-foreground">Categorize the contributor with relevant keywords.</p>
                        <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                        {{-- Note: A dedicated tag input JS component is better UX long-term --}}
                    </div>

                    {{-- === NEW: Social Media === --}}
                    <div class="space-y-2 rounded-md border dark:border-gray-700 p-4">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Social Media Links (Optional)</h4>
                        {{-- Example for Twitter --}}
                        <div class="flex items-center gap-2">
                            <x-lucide-twitter class="w-5 h-5 text-muted-foreground" />
                            <x-ui.input id="social_twitter" name="social_media[twitter]" type="url" class="block w-full text-sm" placeholder="https://twitter.com/username" :value="old('social_media.twitter')" />
                        </div>
                        {{-- Example for LinkedIn --}}
                        <div class="flex items-center gap-2">
                            <x-lucide-linkedin class="w-5 h-5 text-muted-foreground" />
                            <x-ui.input id="social_linkedin" name="social_media[linkedin]" type="url" class="block w-full text-sm" placeholder="https://linkedin.com/in/..." :value="old('social_media.linkedin')" />
                        </div>
                        {{-- Add more for Instagram, Facebook, Website etc. as needed --}}
                        <x-input-error :messages="$errors->get('social_media.*')" class="mt-2 text-xs" /> {{-- Catch errors for any social key --}}
                    </div>


                    {{-- === NEW: Sources === --}}
                    <div>
                        <x-ui.label for="sources">Sources (Optional, one URL per line)</x-ui.label>
                        <x-ui.textarea
                            id="sources"
                            name="sources"
                            rows="3"
                            class="mt-1 block w-full"
                            placeholder="https://example.com/article1
https://anothersource.org/report">
                            {{
    old('sources') 
}}
                        </x-ui.textarea>
                        <p class="mt-1 text-xs text-muted-foreground">Provide links to verify information or contributions.</p>
                        <x-input-error :messages="$errors->get('sources')" class="mt-2" />
                        <x-input-error :messages="$errors->get('sources.*')" class="mt-2 text-xs" /> {{-- Catch errors for individual lines --}}
                    </div>

                    {{-- === NEW: Key Achievements === --}}
                    <div class="mt-4">
                        <x-ui.label for="key_achievements">Key Achievements / Contribution Summary (Optional)</x-ui.label>
                        <x-ui.textarea id="key_achievements" name="key_achievements" rows="3" class="mt-1 block w-full" placeholder="List 2-3 key points related to their campaign adherence or impact...">{{ old('key_achievements') }}</x-ui.textarea>
                        <p class="mt-1 text-xs text-muted-foreground">Highlight specific accomplishments (use bullet points or short sentences).</p>
                        <x-input-error :messages="$errors->get('key_achievements')" class="mt-2" />
                    </div>

                    {{-- === NEW: Featured Article URL === --}}
                    <div class="mt-4">
                        <x-ui.label for="featured_article_url">Featured Article/Link (Optional)</x-ui.label>
                        <x-ui.input id="featured_article_url" name="featured_article_url" type="url" class="mt-1 block w-full" placeholder="https://example.com/relevant-story" :value="old('featured_article_url')" />
                        <p class="mt-1 text-xs text-muted-foreground">Link to a primary news article, profile, or resource about them.</p>
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