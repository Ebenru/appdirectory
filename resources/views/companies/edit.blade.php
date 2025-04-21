{{-- resources/views/companies/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        {{-- Corrected variable name: lagalName -> legalName --}}
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit Submission: {{ $company->displayName ?? $company->legalName }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto py-12 sm:px-6 lg:px-8">
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Update Organization Details</x-ui.card-title>
                <x-ui.card-description>
                    {{-- Use @can directive for better authorization check display --}}
                    @can('manageStatus', $company) {{-- Check if admin --}}
                    You are editing this entry as an Administrator.
                    @elsecan('update', $company) {{-- Check if user can update (pending & owner) --}}
                    You can modify your pending submission here. Changes are saved directly.
                    @else
                    Viewing details. You may not have permission to edit this entry in its current state.
                    @endcan
                </x-ui.card-description>
            </x-ui.card-header>

            <x-ui.card-content>
                {{-- Change action route and add method spoofing --}}
                <form method="POST" action="{{ route('companies.update', $company) }}" class="space-y-6" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH') {{-- Or PUT --}}

                    {{-- Legal Name --}}
                    <div>
                        <x-ui.label for="legalName">Legal Name</x-ui.label>
                        <x-ui.input id="legalName" name="legalName" type="text" class="mt-1 block w-full" :value="old('legalName', $company->legalName)" required autofocus />
                        <x-input-error :messages="$errors->get('legalName')" class="mt-2" />
                    </div>

                    {{-- Display Name --}}
                    <div>
                        <x-ui.label for="displayName">Display Name (Optional)</x-ui.label> {{-- Added Optional --}}
                        <x-ui.input id="displayName" name="displayName" type="text" class="mt-1 block w-full" :value="old('displayName', $company->displayName)" />
                        <x-input-error :messages="$errors->get('displayName')" class="mt-2" />
                    </div>

                    {{-- Category --}}
                    <div>
                        <x-ui.label for="cmpCategory">Category</x-ui.label>
                        <x-ui.select id="cmpCategory" name="cmpCategory" class="mt-1 block w-full" required>
                            <option value="" disabled>Select a category...</option>
                            @foreach($categories as $slug => $name)
                            <option value="{{ $slug }}" @selected(old('cmpCategory', $company->cmpCategory) == $slug)>
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

                    {{-- === EDIT: Country (HQ) === --}}
                    <div>
                        <x-ui.label for="country_id">Primary Country (HQ)</x-ui.label>
                        <x-ui.select id="country_id" name="country_id" class="mt-1 block w-full" required>
                            <option value="" disabled>Select primary country...</option>
                            {{-- Corrected Loop for key => value array --}}
                            @foreach($countries as $country_id => $country_name)
                            <option value="{{ $country_id }}" @selected(old('country_id', $company->country_id) == $country_id)> {{-- Compare against $country_id --}}
                                {{ $country_name }} {{-- Display $country_name --}}
                            </option>
                            @endforeach
                            {{-- End Corrected Loop --}}
                        </x-ui.select>
                        <x-input-error :messages="$errors->get('country_id')" class="mt-2" />
                    </div>

                    {{-- Description --}}
                    <div>
                        <x-ui.label for="description">Contribution / About</x-ui.label>
                        <x-ui.textarea id="description" name="description" rows="4" class="mt-1 block w-full">{{ old('description', $company->description) }}</x-ui.textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    {{-- Website URL --}}
                    <div>
                        <x-ui.label for="website_url">Website URL (Optional)</x-ui.label>
                        <x-ui.input id="website_url" name="website_url" type="url" class="mt-1 block w-full" placeholder="https://..." :value="old('website_url', $company->website_url)" />
                        <x-input-error :messages="$errors->get('website_url')" class="mt-2" />
                    </div>

                    {{-- Display Current Logo --}}
                    {{-- Use the correct accessor display_logo_url --}}
                    @php
                    // Check if display URL is not the default placeholder path before showing
                    $defaultPlaceholder = asset('images/default-logo-placeholder.png');
                    $isDefaultPlaceholder = ($company->display_logo_url === $defaultPlaceholder);
                    @endphp
                    @if($company->display_logo_url && !$isDefaultPlaceholder)
                    <div class="mt-4">
                        <p class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Logo:</p>
                        <div class="h-20 w-auto p-1 inline-block border rounded-md dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                            <img src="{{ $company->display_logo_url }}" alt="Current Logo" class="h-full w-auto object-contain">
                        </div>
                    </div>
                    @endif

                    {{-- Logo URL Input --}}
                    <div class="mt-4">
                        <x-ui.label for="logo_url">Logo URL (Optional - Leave blank to keep current or use uploaded logo)</x-ui.label>
                        <x-ui.input id="logo_url" name="logo_url" type="url" class="mt-1 block w-full" placeholder="https://..." :value="old('logo_url', $company->logo_url)" />
                        <x-input-error :messages="$errors->get('logo_url')" class="mt-2" />
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

                    {{-- Logo Upload Input --}}
                    <div>
                        <x-ui.label for="logo_upload">Upload New Logo (Optional - Replaces existing)</x-ui.label>
                        {{-- Correct input name to 'logo' --}}
                        <x-ui.input id="logo_upload" name="logo" type="file" class="mt-1 block w-full border p-2 text-sm file:mr-4 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20" />
                        <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                    </div>

                    {{-- === EDIT: Tags === --}}
                    <div class="mt-4">
                        <x-ui.label for="tags">Tags (Comma-separated)</x-ui.label>
                        <x-ui.input id="tags" name="tags" type="text" class="mt-1 block w-full" placeholder="e.g., consumer-goods, environment"
                            :value="old('tags', $company->tags->pluck('name')->implode(', '))" />
                        <p class="mt-1 text-xs text-muted-foreground">Categorize the organization with relevant keywords.</p>
                        <x-input-error :messages="$errors->get('tags')" class="mt-2" />
                    </div>

                    {{-- === EDIT: Social Media === --}}
                    <div class="mt-4 space-y-2 rounded-md border dark:border-gray-700 p-4">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Social Media Links (Optional)</h4>
                        <div class="flex items-center gap-2">
                            <x-lucide-twitter class="w-5 h-5 text-muted-foreground" />
                            <x-ui.input name="social_media[twitter]" type="url" class="block w-full text-sm" placeholder="Twitter URL"
                                :value="old('social_media.twitter', $company->social_media['twitter'] ?? '')" />
                        </div>
                        <div class="flex items-center gap-2">
                            <x-lucide-linkedin class="w-5 h-5 text-muted-foreground" />
                            <x-ui.input name="social_media[linkedin]" type="url" class="block w-full text-sm" placeholder="LinkedIn URL"
                                :value="old('social_media.linkedin', $company->social_media['linkedin'] ?? '')" />
                        </div>
                        <x-input-error :messages="$errors->get('social_media.*')" class="mt-2 text-xs" />
                    </div>

                    {{-- === EDIT: Sources === --}}
                    <div class="mt-4">
                        <x-ui.label for="sources">Sources (Optional, one URL per line)</x-ui.label>
                        <x-ui.textarea id="sources" name="sources" rows="3" class="mt-1 block w-full" placeholder="Verification links...">{{ old('sources', implode("\n", $company->sources ?? [])) }}</x-ui.textarea>
                        <p class="mt-1 text-xs text-muted-foreground">Provide links to verify information or contributions.</p>
                        <x-input-error :messages="$errors->get('sources')" class="mt-2" />
                        <x-input-error :messages="$errors->get('sources.*')" class="mt-2 text-xs" />
                    </div>

                    {{-- === EDIT: Key Achievements === --}}
                    <div class="mt-4">
                        <x-ui.label for="key_achievements">Key Achievements / Contribution Summary (Optional)</x-ui.label>
                        <x-ui.textarea id="key_achievements" name="key_achievements" rows="3" class="mt-1 block w-full" placeholder="List 2-3 key points...">{{ old('key_achievements', $company->key_achievements) }}</x-ui.textarea>
                        <p class="mt-1 text-xs text-muted-foreground">Highlight specific accomplishments or contributions.</p>
                        <x-input-error :messages="$errors->get('key_achievements')" class="mt-2" />
                    </div>

                    {{-- === EDIT: Featured Article URL === --}}
                    <div class="mt-4">
                        <x-ui.label for="featured_article_url">Featured Article/Link (Optional)</x-ui.label>
                        <x-ui.input id="featured_article_url" name="featured_article_url" type="url" class="mt-1 block w-full" placeholder="https://example.com/company-feature" :value="old('featured_article_url', $company->featured_article_url)" />
                        <p class="mt-1 text-xs text-muted-foreground">Link to a primary news article or resource about the organization.</p>
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