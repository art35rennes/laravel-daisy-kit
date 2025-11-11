@props([
    'title' => __('profile.settings'),
    'theme' => null,
    // Profile data (agnostic: array, object, or model)
    'profile' => null, // Auto-detect: auth()->user() if available, or passed explicitly
    // Data accessors (for model-agnostic access)
    'preferencesKey' => 'preferences', // Key to access preferences
    'languageKey' => 'language',
    'timezoneKey' => 'timezone',
    // Routes
    'action' => \Illuminate\Support\Facades\Route::has('profile.settings.update') ? route('profile.settings.update') : '#',
    'method' => 'POST',
    'profileEditUrl' => \Illuminate\Support\Facades\Route::has('profile.edit') ? route('profile.edit') : '#',
    'profileViewUrl' => \Illuminate\Support\Facades\Route::has('profile.show') ? route('profile.show') : '#',
    // Sections
    'showPreferences' => true,
    'showNotifications' => true,
    'showSecurity' => true,
    'showPrivacy' => false,
    'showLanguage' => true,
    'showTheme' => true,
    // Preferences data (can be passed separately or accessed from profile)
    'preferences' => null, // ['language' => 'fr', 'timezone' => 'Europe/Paris', ...]
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

    // Get preferences (from separate prop or from profile)
    $prefs = $preferences ?? $getData($preferencesKey, []);
    if (!is_array($prefs)) {
        $prefs = is_object($prefs) ? (array) $prefs : [];
    }

    $language = old('language', $prefs['language'] ?? $getData($languageKey, config('app.locale', 'fr')));
    $timezone = old('timezone', $prefs['timezone'] ?? $getData($timezoneKey, config('app.timezone', 'UTC')));

    // Notification preferences (defaults)
    $notifyEmail = old('notify_email', $prefs['notify_email'] ?? true);
    $notifyPush = old('notify_push', $prefs['notify_push'] ?? false);
    $notifySms = old('notify_sms', $prefs['notify_sms'] ?? false);
    $notifyFeatures = old('notify_features', $prefs['notify_features'] ?? true);
    $notifyMessages = old('notify_messages', $prefs['notify_messages'] ?? true);
    $notifyComments = old('notify_comments', $prefs['notify_comments'] ?? false);
    $notifyMentions = old('notify_mentions', $prefs['notify_mentions'] ?? true);

    // Security preferences
    $twoFactorEnabled = old('two_factor_enabled', $prefs['two_factor_enabled'] ?? false);

    // Theme preference
    $currentTheme = old('theme', $prefs['theme'] ?? session('theme', 'light'));

    // Build breadcrumbs
    $breadcrumbs = [
        ['label' => __('profile.profile'), 'href' => $profileViewUrl !== '#' ? $profileViewUrl : null],
        ['label' => __('profile.settings'), 'href' => null],
    ];

    // Determine HTTP method for form
    $httpMethod = strtoupper($method);
    $needsMethodOverride = in_array($httpMethod, ['PUT', 'PATCH', 'DELETE']);

    // Available languages (from config or default)
    $availableLanguages = config('app.locales', ['fr' => 'Français', 'en' => 'English']);

    // Available timezones
    $availableTimezones = [
        'UTC' => 'UTC',
        'Europe/Paris' => 'Europe/Paris (CET)',
        'Europe/London' => 'Europe/London (GMT)',
        'America/New_York' => 'America/New_York (EST)',
        'America/Los_Angeles' => 'America/Los_Angeles (PST)',
        'Asia/Tokyo' => 'Asia/Tokyo (JST)',
    ];
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
                @if($showPreferences)
                    <x-daisy::ui.layout.crud-section
                        :title="__('profile.preferences')"
                        :description="__('profile.preferences_description')"
                    >
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-base-content/70">{{ __('profile.language') }}</dt>
                                <dd class="mt-1 text-sm text-base-content">{{ $availableLanguages[$language] ?? $language }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-base-content/70">{{ __('profile.timezone') }}</dt>
                                <dd class="mt-1 text-sm text-base-content">{{ $availableTimezones[$timezone] ?? $timezone }}</dd>
                            </div>
                        </dl>
                    </x-daisy::ui.layout.crud-section>
                @endif

                @if($showNotifications)
                    <x-daisy::ui.layout.crud-section
                        :title="__('profile.notifications')"
                        :description="__('profile.notifications_description')"
                        :borderTop="$showPreferences"
                    >
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-base-content/70">{{ __('profile.email_notifications') }}</dt>
                                <dd class="mt-1 text-sm text-base-content">{{ $notifyEmail ? __('Yes') : __('No') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-base-content/70">{{ __('profile.push_notifications') }}</dt>
                                <dd class="mt-1 text-sm text-base-content">{{ $notifyPush ? __('Yes') : __('No') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-base-content/70">{{ __('profile.sms_notifications') }}</dt>
                                <dd class="mt-1 text-sm text-base-content">{{ $notifySms ? __('Yes') : __('No') }}</dd>
                            </div>
                        </dl>
                    </x-daisy::ui.layout.crud-section>
                @endif

                @if($showSecurity)
                    <x-daisy::ui.layout.crud-section
                        :title="__('profile.security')"
                        :description="__('profile.security_description')"
                        :borderTop="($showPreferences || $showNotifications)"
                    >
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-base-content/70">{{ __('profile.two_factor_auth') }}</dt>
                                <dd class="mt-1 text-sm text-base-content">{{ $twoFactorEnabled ? __('Yes') : __('No') }}</dd>
                            </div>
                        </dl>
                    </x-daisy::ui.layout.crud-section>
                @endif

                @if($showTheme)
                    <x-daisy::ui.layout.crud-section
                        :title="__('profile.appearance')"
                        :description="__('profile.appearance_description')"
                        :borderTop="($showPreferences || $showNotifications || $showSecurity)"
                    >
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-base-content/70">{{ __('profile.theme') }}</dt>
                                <dd class="mt-1 text-sm text-base-content">{{ ucfirst(str_replace('_', ' ', $currentTheme)) }}</dd>
                            </div>
                        </dl>
                    </x-daisy::ui.layout.crud-section>
                @endif
            </x-daisy::ui.layout.crud-layout>
        @else
            {{-- Edit mode: form --}}
            <form action="{{ $action }}" method="POST">
                @csrf
                @if($needsMethodOverride)
                    @method($httpMethod)
                @endif

                <x-daisy::ui.layout.crud-layout>
                    @if($showPreferences)
                        <x-daisy::ui.layout.crud-section
                            :title="__('profile.preferences')"
                            :description="__('profile.preferences_description')"
                        >
                            <div class="space-y-4">
                                <x-daisy::ui.partials.form-field name="language" :label="__('profile.language')" :required="false">
                                    <x-daisy::ui.inputs.select
                                        name="language"
                                        :class="$errors->has('language') ? 'select-error' : ''"
                                    >
                                        @foreach($availableLanguages as $code => $label)
                                            <option value="{{ $code }}" @selected($language === $code)>{{ $label }}</option>
                                        @endforeach
                                    </x-daisy::ui.inputs.select>
                                </x-daisy::ui.partials.form-field>

                                <x-daisy::ui.partials.form-field name="timezone" :label="__('profile.timezone')" :required="false">
                                    <x-daisy::ui.inputs.select
                                        name="timezone"
                                        :class="$errors->has('timezone') ? 'select-error' : ''"
                                    >
                                        @foreach($availableTimezones as $code => $label)
                                            <option value="{{ $code }}" @selected($timezone === $code)>{{ $label }}</option>
                                        @endforeach
                                    </x-daisy::ui.inputs.select>
                                </x-daisy::ui.partials.form-field>
                            </div>
                        </x-daisy::ui.layout.crud-section>
                    @endif

                    @if($showNotifications)
                        <x-daisy::ui.layout.crud-section
                            :title="__('profile.notifications')"
                            :description="__('profile.notifications_description')"
                            :borderTop="$showPreferences"
                        >
                            <div class="space-y-6">
                                <div class="space-y-4">
                                    <x-daisy::ui.partials.form-field name="notify_email" :label="__('profile.email_notifications')" :required="false">
                                        <div class="flex items-center gap-2">
                                            <x-daisy::ui.inputs.toggle name="notify_email" :checked="$notifyEmail" />
                                            <span class="text-sm text-base-content/70">{{ __('profile.receive_notifications_by_email') }}</span>
                                        </div>
                                    </x-daisy::ui.partials.form-field>

                                    <x-daisy::ui.partials.form-field name="notify_push" :label="__('profile.push_notifications')" :required="false">
                                        <div class="flex items-center gap-2">
                                            <x-daisy::ui.inputs.toggle name="notify_push" :checked="$notifyPush" />
                                            <span class="text-sm text-base-content/70">{{ __('profile.receive_push_notifications') }}</span>
                                        </div>
                                    </x-daisy::ui.partials.form-field>

                                    <x-daisy::ui.partials.form-field name="notify_sms" :label="__('profile.sms_notifications')" :required="false">
                                        <div class="flex items-center gap-2">
                                            <x-daisy::ui.inputs.toggle name="notify_sms" :checked="$notifySms" />
                                            <span class="text-sm text-base-content/70">{{ __('profile.receive_sms_notifications') }}</span>
                                        </div>
                                    </x-daisy::ui.partials.form-field>
                                </div>

                                <div class="divider"></div>

                                <div class="space-y-3">
                                    <h3 class="text-sm font-medium">{{ __('profile.notification_types') }}</h3>
                                    <div class="space-y-2">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <x-daisy::ui.inputs.checkbox name="notify_features" :checked="$notifyFeatures" />
                                            <span class="text-sm">{{ __('profile.new_features') }}</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <x-daisy::ui.inputs.checkbox name="notify_messages" :checked="$notifyMessages" />
                                            <span class="text-sm">{{ __('profile.messages') }}</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <x-daisy::ui.inputs.checkbox name="notify_comments" :checked="$notifyComments" />
                                            <span class="text-sm">{{ __('profile.comments') }}</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <x-daisy::ui.inputs.checkbox name="notify_mentions" :checked="$notifyMentions" />
                                            <span class="text-sm">{{ __('profile.mentions') }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </x-daisy::ui.layout.crud-section>
                    @endif

                    @if($showSecurity)
                        <x-daisy::ui.layout.crud-section
                            :title="__('profile.security')"
                            :description="__('profile.security_description')"
                            :borderTop="($showPreferences || $showNotifications)"
                        >
                            <div class="space-y-6">
                                <x-daisy::ui.advanced.collapse>
                                    <x-slot:title>
                                        <span class="font-semibold">{{ __('profile.change_password') }}</span>
                                    </x-slot:title>
                                    <div class="space-y-4 pt-2">
                                        <x-daisy::ui.partials.form-field name="current_password" :label="__('profile.current_password')" :required="false">
                                            <x-daisy::ui.inputs.input
                                                name="current_password"
                                                type="password"
                                                autocomplete="current-password"
                                                :class="$errors->has('current_password') ? 'input-error' : ''"
                                            />
                                        </x-daisy::ui.partials.form-field>

                                        <x-daisy::ui.partials.form-field name="password" :label="__('profile.new_password')" :required="false">
                                            <x-daisy::ui.inputs.input
                                                name="password"
                                                type="password"
                                                autocomplete="new-password"
                                                :class="$errors->has('password') ? 'input-error' : ''"
                                            />
                                        </x-daisy::ui.partials.form-field>

                                        <x-daisy::ui.partials.form-field name="password_confirmation" :label="__('profile.confirm_new_password')" :required="false">
                                            <x-daisy::ui.inputs.input
                                                name="password_confirmation"
                                                type="password"
                                                autocomplete="new-password"
                                                :class="$errors->has('password_confirmation') ? 'input-error' : ''"
                                            />
                                        </x-daisy::ui.partials.form-field>
                                    </div>
                                </x-daisy::ui.advanced.collapse>

                                <div class="divider"></div>

                                <x-daisy::ui.partials.form-field name="two_factor_enabled" :label="__('profile.two_factor_auth')" :required="false">
                                    <div class="flex items-center gap-2">
                                        <x-daisy::ui.inputs.toggle name="two_factor_enabled" :checked="$twoFactorEnabled" />
                                        <span class="text-sm text-base-content/70">{{ __('profile.enable_two_factor_authentication') }}</span>
                                    </div>
                                </x-daisy::ui.partials.form-field>
                            </div>
                        </x-daisy::ui.layout.crud-section>
                    @endif

                    @if($showPrivacy)
                        <x-daisy::ui.layout.crud-section
                            :title="__('profile.privacy')"
                            :description="__('profile.privacy_description')"
                            :borderTop="($showPreferences || $showNotifications || $showSecurity)"
                        >
                            <p class="text-base-content/70">{{ __('profile.coming_soon') }}</p>
                        </x-daisy::ui.layout.crud-section>
                    @endif

                    @if($showTheme)
                        <x-daisy::ui.layout.crud-section
                            :title="__('profile.appearance')"
                            :description="__('profile.appearance_description')"
                            :borderTop="($showPreferences || $showNotifications || $showSecurity || $showPrivacy)"
                        >
                            <div class="space-y-4">
                                <x-daisy::ui.partials.form-field name="theme" :label="__('profile.theme')" :required="false">
                                    <x-daisy::ui.inputs.select
                                        name="theme"
                                        :class="$errors->has('theme') ? 'select-error' : ''"
                                    >
                                        @php
                                            $availableThemes = [
                                                'light' => __('profile.theme_light'),
                                                'dark' => __('profile.theme_dark'),
                                                'cupcake' => 'Cupcake',
                                                'bumblebee' => 'Bumblebee',
                                                'emerald' => 'Emerald',
                                                'corporate' => 'Corporate',
                                                'synthwave' => 'Synthwave',
                                                'retro' => 'Retro',
                                                'cyberpunk' => 'Cyberpunk',
                                                'valentine' => 'Valentine',
                                                'halloween' => 'Halloween',
                                                'garden' => 'Garden',
                                                'forest' => 'Forest',
                                                'aqua' => 'Aqua',
                                                'lofi' => 'Lofi',
                                                'pastel' => 'Pastel',
                                                'fantasy' => 'Fantasy',
                                                'wireframe' => 'Wireframe',
                                                'black' => 'Black',
                                                'luxury' => 'Luxury',
                                                'dracula' => 'Dracula',
                                                'cmyk' => 'CMYK',
                                                'autumn' => 'Autumn',
                                                'business' => 'Business',
                                                'acid' => 'Acid',
                                                'lemonade' => 'Lemonade',
                                                'night' => 'Night',
                                                'coffee' => 'Coffee',
                                                'winter' => 'Winter',
                                            ];
                                        @endphp
                                        @foreach($availableThemes as $themeValue => $themeLabel)
                                            <option value="{{ $themeValue }}" @selected($currentTheme === $themeValue)>{{ $themeLabel }}</option>
                                        @endforeach
                                    </x-daisy::ui.inputs.select>
                                </x-daisy::ui.partials.form-field>
                            </div>
                        </x-daisy::ui.layout.crud-section>
                    @endif

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
    </div>
</x-daisy::layout.app>
