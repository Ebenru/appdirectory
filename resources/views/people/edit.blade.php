{{-- resources/views/people/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit Submission: {{ $person->fullName }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto py-12 sm:px-6 lg:px-8">
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Update Person Details</x-ui.card-title>
                <x-ui.card-description>
                    {{-- Adjust message based on who is editing and status --}}
                    @can('manageStatus', $person) {{-- Check if admin can manage status --}}
                    You are editing this entry as an Administrator.
                    @elsecan('update', $person) {{-- Check if user can update (pending & owner) --}}
                    You can modify your pending submission here. Changes are saved directly.
                    @else
                    Viewing details. You may not have permission to edit this entry in its current state.
                    @endcan
                </x-ui.card-description>
            </x-ui.card-header>
            <x-ui.card-content>
                {{-- Change action route and add method spoofing --}}
                <form method="POST" action="{{ route('people.update', $person) }}" class="space-y-6" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH') {{-- Or PUT --}}

                    {{-- Full Name --}}
                    <div>
                        <x-ui.label for="fullName">Full Name</x-ui.label>
                        <x-ui.input id="fullName" name="fullName" type="text" class="mt-1 block w-full" :value="old('fullName', $person->fullName)" required autofocus />
                        <x-input-error :messages="$errors->get('fullName')" class="mt-2" />
                    </div>

                    {{-- Title --}}
                    <div>
                        <x-ui.label for="title">Title / Role</x-ui.label>
                        <x-ui.input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $person->title)" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    {{-- Category --}}
                    <div>
                        <x-ui.label for="pplCategory">Category</x-ui.label>
                        <x-ui.select id="pplCategory" name="pplCategory" class="mt-1 block w-full" required>
                            <option value="" disabled>Select a category...</option>
                            @foreach($categories as $slug => $name)
                            <option value="{{ $slug }}" @selected(old('pplCategory', $person->pplCategory) == $slug)>
                                {{ $name }}
                            </option>
                            @endforeach
                        </x-ui.select>
                        <x-input-error :messages="$errors->get('pplCategory')" class="mt-2" />
                    </div>

                    {{-- === EDIT: Country === --}}
                    <div>
                        <x-ui.label for="country_id">Primary Country</x-ui.label>
                        {{-- $countries passed from controller using pluck('name', 'id') --}}
                        <x-ui.select id="country_id" name="country_id" class="mt-1 block w-full" required>
                            <option value="" disabled {{-- Don't select the placeholder by default on edit --}}>Select primary country...</option>
                            {{-- Corrected loop for key => value array --}}
                            @foreach($countries as $country_id => $country_name)
                            <option value="{{ $country_id }}" @selected(old('country_id', $person->country_id) == $country_id)> {{-- Compare against $country_id --}}
                                {{ $country_name }} {{-- Display $country_name --}}
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
                        <x-ui.label for="description">Contribution / About</x-ui.label>
                        <x-ui.textarea id="description" name="description" rows="4" class="mt-1 block w-full">{{ old('description', $person->description) }}</x-ui.textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    {{-- Display Current Photo --}}
                    {{-- Check if the display URL is not the default placeholder --}}
                    @if($person->display_photo_url && !Str::contains($person->display_photo_url, 'ui-avatars.com'))
                    <div class="mt-4">
                        <p class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Photo:</p>
                        <img src="{{ $person->display_photo_url }}" alt="Current Photo" class="h-20 w-auto rounded-md object-contain border dark:border-gray-700">
                    </div>
                    @endif

                    {{-- Picture URL --}}
                    <div class="mt-4">
                        <x-ui.label for="picture_url">Picture URL (Optional - Leave blank to keep current or use uploaded photo)</x-ui.label>
                        <x-ui.input id="picture_url" name="picture_url" type="url" class="mt-1 block w-full" placeholder="https://..." :value="old('picture_url', $person->picture_url)" />
                        <x-input-error :messages="$errors->get('picture_url')" class="mt-2" />
                    </div>

                    {{-- OR Separator --}}
                    <div class="relative my-4">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
                        </div>
                        <div class="relative flex justify-center">
                            <span class="bg-card px-2 text-sm text-muted-foreground">OR</span>
                        </div>
                    </div>

                    {{-- Picture Upload --}}
                    <div>
                        <x-ui.label for="photo_upload">Upload New Picture (Optional - Replaces existing)</x-ui.label>
                        {{-- Ensure consistent styling from create form --}}
                        <x-ui.input id="photo_upload" name="photo" type="file" class="mt-1 block w-full border p-2 text-sm file:mr-4 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20" />
                        <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                    </div>

                    {{-- === EDIT: Tags === --}}
                    <div class="mt-4">
                        <x-ui.label for="tags">Tags (Comma-separated)</x-ui.label>
                        {{-- Pre-fill with existing tag names --}}
                        <x-ui.input id="tags" name="tags" type="text" class="mt-1 block w-full" placeholder="e.g., human-rights, tech, activism"
                            :value="old('tags', $person->tags->pluck('name')->implode(', '))" {{-- Pluck names and join --}} />
                        <p class="mt-1 text-xs text-muted-foreground">Categorize the contributor with relevant keywords.</p>
                        <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                    </div>

                    {{-- === EDIT: Social Media === --}}
                    <div class="mt-4 space-y-2 rounded-md border dark:border-gray-700 p-4">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Social Media Links (Optional)</h4>
                        {{-- Pre-fill Twitter --}}
                        <div class="flex items-center gap-2">
                            <x-lucide-twitter class="w-5 h-5 text-muted-foreground" />
                            <x-ui.input name="social_media[twitter]" type="url" class="block w-full text-sm" placeholder="Twitter URL"
                                :value="old('social_media.twitter', $person->social_media['twitter'] ?? '')" {{-- Access array key --}} />
                        </div>
                        {{-- Pre-fill LinkedIn --}}
                        <div class="flex items-center gap-2">
                            <x-lucide-linkedin class="w-5 h-5 text-muted-foreground" />
                            <x-ui.input name="social_media[linkedin]" type="url" class="block w-full text-sm" placeholder="LinkedIn URL"
                                :value="old('social_media.linkedin', $person->social_media['linkedin'] ?? '')" />
                        </div>
                        {{-- Add more platforms if needed --}}
                        <x-input-error :messages="$errors->get('social_media.*')" class="mt-2 text-xs" />
                    </div>

                    {{-- === EDIT: Sources === --}}
                    <div class="mt-4">
                        <x-ui.label for="sources">Sources (Optional, one URL per line)</x-ui.label>
                        {{-- Pre-fill textarea by joining array elements with newline --}}
                        <x-ui.textarea id="sources" name="sources" rows="3" class="mt-1 block w-full" placeholder="https://example.com/article1
https://anothersource.org/report">{{ old('sources', implode("\n", $person->sources ?? [])) }}</x-ui.textarea>
                        <p class="mt-1 text-xs text-muted-foreground">Provide links to verify information or contributions.</p>
                        <x-input-error :messages="$errors->get('sources')" class="mt-2" />
                        <x-input-error :messages="$errors->get('sources.*')" class="mt-2 text-xs" />
                    </div>
                    {{-- === EDIT: Key Achievements === --}}
                    <div class="mt-4">
                        <x-ui.label for="key_achievements">Key Achievements / Contribution Summary (Optional)</x-ui.label>
                        <x-ui.textarea id="key_achievements" name="key_achievements" rows="3" class="mt-1 block w-full" placeholder="List 2-3 key points...">{{ old('key_achievements', $person->key_achievements) }}</x-ui.textarea>
                        <p class="mt-1 text-xs text-muted-foreground">Highlight specific accomplishments (use bullet points or short sentences).</p>
                        <x-input-error :messages="$errors->get('key_achievements')" class="mt-2" />
                    </div>

                    {{-- === EDIT: Featured Article URL === --}}
                    <div class="mt-4">
                        <x-ui.label for="featured_article_url">Featured Article/Link (Optional)</x-ui.label>
                        <x-ui.input id="featured_article_url" name="featured_article_url" type="url" class="mt-1 block w-full" placeholder="https://example.com/relevant-story" :value="old('featured_article_url', $person->featured_article_url)" />
                        <p class="mt-1 text-xs text-muted-foreground">Link to a primary news article, profile, or resource about them.</p>
                        <x-input-error :messages="$errors->get('featured_article_url')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end pt-6">
                        <x-ui.button type="submit">
                            {{ __('Save Changes') }}
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card-content>
        </x-ui.card>
    </div>
</x-app-layout>