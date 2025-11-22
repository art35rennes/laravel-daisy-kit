@props([
    'title' => __('profile.edit_profile'),
    'theme' => null,
    // Profile data (agnostic: array, object, or model)
    'profile' => null, // Auto-detect: auth()->user() if available, or passed explicitly
    // Data accessors (for model-agnostic access)
    'nameKey' => 'name',
    'emailKey' => 'email',
    'avatarKey' => 'avatar',
    'phoneKey' => 'phone',
    'bioKey' => 'bio',
    'locationKey' => 'location',
    'websiteKey' => 'website',
    // Form
    'action' => \Illuminate\Support\Facades\Route::has('profile.update') ? route('profile.update') : '#',
    'method' => 'POST',
    'enctype' => 'multipart/form-data',
    // Routes
    'profileViewUrl' => \Illuminate\Support\Facades\Route::has('profile.show') ? route('profile.show') : '#',
    'profileSettingsUrl' => \Illuminate\Support\Facades\Route::has('profile.settings') ? route('profile.settings') : '#',
    // Options
    'showAvatar' => true,
    'showName' => true,
    'showEmail' => true,
    'showPhone' => false,
    'showBio' => true,
    'showLocation' => false,
    'showWebsite' => false,
    'avatarMaxSize' => 2048, // KB
    'avatarAcceptedTypes' => ['image/jpeg', 'image/png', 'image/webp'],
    // Readonly mode
    'readonly' => false,
])

@php
    // Auto-detect profile if not provided
    if (is_null($profile) && auth()->check()) {
        $profile = auth()->user();
    }

    // Helper function to get data agnostically
    $getData = function($key, $default = null) use ($profile) {
        if (is_null($profile)) {
            return $default;
        }
        return data_get($profile, $key, $default);
    };

    $name = $getData($nameKey, old('name'));
    $email = $getData($emailKey, old('email'));
    $avatar = $getData($avatarKey);
    $phone = $getData($phoneKey, old('phone'));
    $bio = $getData($bioKey, old('bio'));
    $location = $getData($locationKey, old('location'));
    $website = $getData($websiteKey, old('website'));

    // Handle avatar URL (if stored in storage, use Storage::url)
    if ($avatar && !filter_var($avatar, FILTER_VALIDATE_URL) && str_starts_with($avatar, 'storage/')) {
        $avatar = \Illuminate\Support\Facades\Storage::url($avatar);
    }

    // Build breadcrumbs
    $breadcrumbs = [
        ['label' => __('profile.profile'), 'href' => $profileViewUrl !== '#' ? $profileViewUrl : null],
        ['label' => __('profile.edit_profile'), 'href' => null],
    ];

    // Determine HTTP method for form
    $httpMethod = strtoupper($method);
    $needsMethodOverride = in_array($httpMethod, ['PUT', 'PATCH', 'DELETE']);
@endphp

<x-daisy::layout.app :title="$title" :theme="$theme" :container="true">
    @isset($header)
        {{ $header }}
    @else
        <x-daisy::ui.navigation.breadcrumbs :items="$breadcrumbs" />
    @endisset

    <div class="space-y-6">
        {{-- Success/Error Messages --}}
        @if(session('status'))
            <x-daisy::ui.feedback.alert color="success">
                {{ session('status') }}
            </x-daisy::ui.feedback.alert>
        @endif

        @if($errors->any())
            <x-daisy::ui.feedback.alert color="error">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-daisy::ui.feedback.alert>
        @endif

        @if($readonly)
            {{-- Readonly mode: display only --}}
            <x-daisy::ui.layout.crud-layout>
                @if($showAvatar)
                    <x-daisy::ui.layout.crud-section
                        :title="__('profile.avatar')"
                        :description="__('profile.current_avatar')"
                    >
                        @if($avatar)
                            <x-daisy::ui.data-display.avatar
                                :src="$avatar"
                                :alt="$name"
                                size="lg"
                                :placeholder="mb_substr($name ?? 'A', 0, 1)"
                            />
                        @else
                            <p class="text-base-content/50 italic">{{ __('profile.no_avatar') }}</p>
                        @endif
                    </x-daisy::ui.layout.crud-section>
                @endif

                <x-daisy::ui.layout.crud-section
                    :title="__('profile.profile_information')"
                    :description="__('profile.profile_information_description')"
                    :borderTop="$showAvatar"
                >
                    <dl class="space-y-4">
                        @if($showName)
                            <div>
                                <dt class="text-sm font-medium text-base-content/70">{{ __('profile.name') }}</dt>
                                <dd class="mt-1 text-sm text-base-content">{{ $name ?? '-' }}</dd>
                            </div>
                        @endif

                        @if($showEmail)
                            <div>
                                <dt class="text-sm font-medium text-base-content/70">{{ __('profile.email') }}</dt>
                                <dd class="mt-1 text-sm text-base-content">{{ $email ?? '-' }}</dd>
                            </div>
                        @endif

                        @if($showPhone && $phone)
                            <div>
                                <dt class="text-sm font-medium text-base-content/70">{{ __('profile.phone') }}</dt>
                                <dd class="mt-1 text-sm text-base-content">{{ $phone }}</dd>
                            </div>
                        @endif

                        @if($showBio && $bio)
                            <div>
                                <dt class="text-sm font-medium text-base-content/70">{{ __('profile.bio') }}</dt>
                                <dd class="mt-1 text-sm text-base-content whitespace-pre-line">{{ $bio }}</dd>
                            </div>
                        @endif

                        @if($showLocation && $location)
                            <div>
                                <dt class="text-sm font-medium text-base-content/70">{{ __('profile.location') }}</dt>
                                <dd class="mt-1 text-sm text-base-content">{{ $location }}</dd>
                            </div>
                        @endif

                        @if($showWebsite && $website)
                            <div>
                                <dt class="text-sm font-medium text-base-content/70">{{ __('profile.website') }}</dt>
                                <dd class="mt-1 text-sm text-base-content">
                                    <a href="{{ $website }}" target="_blank" rel="noopener noreferrer" class="link link-hover">
                                        {{ $website }}
                                    </a>
                                </dd>
                            </div>
                        @endif
                    </dl>
                </x-daisy::ui.layout.crud-section>
            </x-daisy::ui.layout.crud-layout>
        @else
            {{-- Edit mode: form --}}
            <form action="{{ $action }}" method="POST" enctype="{{ $enctype }}">
                @csrf
                @if($needsMethodOverride)
                    @method($httpMethod)
                @endif

                <x-daisy::ui.layout.crud-layout>
                    @if($showAvatar)
                        <x-daisy::ui.layout.crud-section
                            :title="__('profile.avatar')"
                            :description="__('profile.upload_avatar')"
                        >
                            @if($avatar)
                                <div class="mb-4">
                                    <x-daisy::ui.data-display.avatar
                                        :src="$avatar"
                                        :alt="$name"
                                        size="lg"
                                        :placeholder="mb_substr($name ?? 'A', 0, 1)"
                                    />
                                    <p class="text-xs text-base-content/70 mt-2">{{ __('profile.current_avatar') }}</p>
                                </div>
                            @endif

                            <x-daisy::ui.partials.form-field name="avatar" :label="__('profile.upload_avatar')" :required="false">
                                <x-daisy::ui.inputs.file-input
                                    name="avatar"
                                    accept="{{ implode(',', $avatarAcceptedTypes) }}"
                                    :class="$errors->has('avatar') ? 'file-input-error' : ''"
                                />
                                <x-slot:hint>
                                    {{ __('profile.avatar_max_size', ['size' => $avatarMaxSize]) }}
                                    <br>
                                    {{ __('profile.avatar_accepted_types', ['types' => implode(', ', array_map(fn($t) => strtoupper(str_replace('image/', '', $t)), $avatarAcceptedTypes))]) }}
                                </x-slot:hint>
                            </x-daisy::ui.partials.form-field>
                        </x-daisy::ui.layout.crud-section>
                    @endif

                    <x-daisy::ui.layout.crud-section
                        :title="__('profile.profile_information')"
                        :description="__('profile.profile_information_description')"
                        :borderTop="$showAvatar"
                    >
                        <div class="space-y-4">
                            @if($showName)
                                <x-daisy::ui.partials.form-field name="name" :label="__('profile.name')" :required="true">
                                    <x-daisy::ui.inputs.input
                                        name="name"
                                        type="text"
                                        :value="old('name', $name)"
                                        autocomplete="name"
                                        placeholder="{{ __('profile.name') }}"
                                        :class="$errors->has('name') ? 'input-error' : ''"
                                    />
                                </x-daisy::ui.partials.form-field>
                            @endif

                            @if($showEmail)
                                <x-daisy::ui.partials.form-field name="email" :label="__('profile.email')" :required="true">
                                    <x-daisy::ui.inputs.input
                                        name="email"
                                        type="email"
                                        :value="old('email', $email)"
                                        autocomplete="email"
                                        placeholder="email@example.com"
                                        :class="$errors->has('email') ? 'input-error' : ''"
                                    />
                                </x-daisy::ui.partials.form-field>
                            @endif

                            @if($showPhone)
                                <x-daisy::ui.partials.form-field name="phone" :label="__('profile.phone')" :required="false">
                                    <x-daisy::ui.inputs.input
                                        name="phone"
                                        type="tel"
                                        :value="old('phone', $phone)"
                                        autocomplete="tel"
                                        placeholder="+33 1 23 45 67 89"
                                        :class="$errors->has('phone') ? 'input-error' : ''"
                                    />
                                </x-daisy::ui.partials.form-field>
                            @endif

                            @if($showBio)
                                <x-daisy::ui.partials.form-field name="bio" :label="__('profile.bio')" :required="false">
                                    <x-daisy::ui.inputs.textarea
                                        name="bio"
                                        rows="4"
                                        placeholder="{{ __('profile.bio') }}"
                                        :class="$errors->has('bio') ? 'textarea-error' : ''"
                                    >{{ old('bio', $bio) }}</x-daisy::ui.inputs.textarea>
                                </x-daisy::ui.partials.form-field>
                            @endif

                            @if($showLocation)
                                <x-daisy::ui.partials.form-field name="location" :label="__('profile.location')" :required="false">
                                    <x-daisy::ui.inputs.input
                                        name="location"
                                        type="text"
                                        :value="old('location', $location)"
                                        autocomplete="address-level2"
                                        placeholder="{{ __('profile.location') }}"
                                        :class="$errors->has('location') ? 'input-error' : ''"
                                    />
                                </x-daisy::ui.partials.form-field>
                            @endif

                            @if($showWebsite)
                                <x-daisy::ui.partials.form-field name="website" :label="__('profile.website')" :required="false">
                                    <x-daisy::ui.inputs.input
                                        name="website"
                                        type="url"
                                        :value="old('website', $website)"
                                        autocomplete="url"
                                        placeholder="https://example.com"
                                        :class="$errors->has('website') ? 'input-error' : ''"
                                    />
                                </x-daisy::ui.partials.form-field>
                            @endif
                        </div>
                    </x-daisy::ui.layout.crud-section>

                    <x-slot:actions>
                        @if($profileViewUrl !== '#')
                            <x-daisy::ui.inputs.button tag="a" :href="$profileViewUrl" variant="outline">
                                {{ __('profile.cancel') }}
                            </x-daisy::ui.inputs.button>
                        @endif
                        <x-daisy::ui.inputs.button type="submit" variant="solid">
                            {{ __('profile.save') }}
                        </x-daisy::ui.inputs.button>
                    </x-slot:actions>
                </x-daisy::ui.layout.crud-layout>
            </form>
        @endif

        {{-- Additional Actions Slot --}}
        @isset($actions)
            <div class="mt-4">
                {{ $actions }}
            </div>
        @endisset
    </div>
</x-daisy::layout.app>
