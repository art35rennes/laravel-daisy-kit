@props([
    'title' => __('profile.profile'),
    'theme' => null,
    // Profile data (agnostic: array, object, or model)
    'profile' => null, // Auto-detect: auth()->user() if available, or passed explicitly
    // Data accessors (for model-agnostic access)
    'nameKey' => 'name',
    'emailKey' => 'email',
    'avatarKey' => 'avatar',
    'bioKey' => 'bio',
    'locationKey' => 'location',
    'websiteKey' => 'website',
    'createdAtKey' => 'created_at', // For "member since"
    'lastActiveKey' => 'last_active_at', // For "last active"
    // Routes
    'profileEditUrl' => \Illuminate\Support\Facades\Route::has('profile.edit') ? route('profile.edit') : '#',
    'profileSettingsUrl' => \Illuminate\Support\Facades\Route::has('profile.settings') ? route('profile.settings') : '#',
    // Data (can be passed or computed - agnostic format)
    'stats' => [], // ['label' => 'Posts', 'value' => 42, 'icon' => 'file-text']
    'badges' => [], // ['label' => 'Early Adopter', 'color' => 'primary', 'icon' => 'star']
    'timeline' => [], // Events/activities: ['date' => '2024-01-15', 'title' => '...', 'icon' => '...']
    'showStats' => true,
    'showBadges' => true,
    'showTimeline' => true,
    'showBio' => true,
    'showContact' => true,
    // Comparison function for isOwnProfile (agnostic)
    'isOwnProfile' => null, // Auto-detect: compare profile with auth()->user() or use custom function
    'compareProfile' => null, // Callable: function($profile) { return $profile->id === auth()->id(); }
])

@php
    // Auto-detect profile if not provided
    if (is_null($profile) && auth()->check()) {
        $profile = auth()->user();
    }

    // Determine if this is the user's own profile
    if (is_null($isOwnProfile)) {
        if (is_callable($compareProfile)) {
            $isOwnProfile = $compareProfile($profile);
        } elseif ($profile && auth()->check()) {
            $profileId = data_get($profile, 'id');
            $isOwnProfile = $profileId && $profileId === auth()->id();
        } else {
            $isOwnProfile = false;
        }
    }

    // Helper function to get data agnostically
    $getData = function($key, $default = null) use ($profile) {
        if (is_null($profile)) {
            return $default;
        }
        return data_get($profile, $key, $default);
    };

    $name = $getData($nameKey);
    $email = $getData($emailKey);
    $avatar = $getData($avatarKey);
    $bio = $getData($bioKey);
    $location = $getData($locationKey);
    $website = $getData($websiteKey);
    $createdAt = $getData($createdAtKey);
    $lastActive = $getData($lastActiveKey);

    // Format dates if they are Carbon instances or date strings
    if ($createdAt && ($createdAt instanceof \Carbon\Carbon || is_string($createdAt))) {
        try {
            $createdAt = $createdAt instanceof \Carbon\Carbon ? $createdAt : \Carbon\Carbon::parse($createdAt);
            $createdAt = $createdAt->format('d/m/Y');
        } catch (\Exception $e) {
            // Keep original value if parsing fails
        }
    }

    if ($lastActive && ($lastActive instanceof \Carbon\Carbon || is_string($lastActive))) {
        try {
            $lastActive = $lastActive instanceof \Carbon\Carbon ? $lastActive : \Carbon\Carbon::parse($lastActive);
            $lastActive = $lastActive->diffForHumans();
        } catch (\Exception $e) {
            // Keep original value if parsing fails
        }
    }

    // Build breadcrumbs
    $breadcrumbs = [
        ['label' => __('profile.profile'), 'href' => null],
    ];
@endphp

<x-daisy::layout.app :title="$title" :theme="$theme" :container="true">
    @isset($header)
        {{ $header }}
    @else
        <x-daisy::ui.navigation.breadcrumbs :items="$breadcrumbs" />
    @endisset

    <div class="space-y-6">
        {{-- Profile Header --}}
        <div class="flex flex-col sm:flex-row gap-6 items-start sm:items-center pb-6 border-b border-base-200">
            <x-daisy::ui.data-display.avatar
                :src="$avatar"
                :alt="$name"
                size="xl"
                :placeholder="mb_substr($name ?? 'A', 0, 1)"
            />

            <div class="flex-1 space-y-2">
                <h1 class="text-3xl font-bold">{{ $name ?? __('profile.profile') }}</h1>
                @if($email)
                    <p class="text-base-content/70">{{ $email }}</p>
                @endif
                @if($bio && $showBio)
                    <p class="text-base-content/80">{{ $bio }}</p>
                @elseif($showBio)
                    <p class="text-base-content/50 italic">{{ __('profile.no_bio') }}</p>
                @endif
                <div class="flex flex-wrap gap-4 text-sm text-base-content/60">
                    @if($createdAt)
                        <span>{{ __('profile.member_since') }}: {{ $createdAt }}</span>
                    @endif
                    @if($lastActive)
                        <span>{{ __('profile.last_active') }}: {{ $lastActive }}</span>
                    @endif
                </div>
            </div>

            @if($isOwnProfile)
                <div class="flex gap-2">
                    @if($profileEditUrl !== '#')
                        <x-daisy::ui.inputs.button :href="$profileEditUrl" variant="outline">
                            {{ __('profile.edit_profile') }}
                        </x-daisy::ui.inputs.button>
                    @endif
                    @if($profileSettingsUrl !== '#')
                        <x-daisy::ui.inputs.button :href="$profileSettingsUrl" variant="outline">
                            {{ __('profile.settings') }}
                        </x-daisy::ui.inputs.button>
                    @endif
                </div>
            @endif
        </div>

        {{-- Content Sections --}}
        <x-daisy::ui.layout.crud-layout>
            {{-- Stats --}}
            @if($showStats && count($stats) > 0)
                <x-daisy::ui.layout.crud-section
                    :title="__('profile.stats')"
                    :description="__('profile.stats_description')"
                >
                    <div class="stats stats-vertical sm:stats-horizontal shadow w-full">
                        @foreach($stats as $stat)
                            <x-daisy::ui.data-display.stat
                                :title="$stat['label'] ?? null"
                                :value="$stat['value'] ?? null"
                                :desc="$stat['desc'] ?? null"
                            >
                                @if(!empty($stat['icon']))
                                    <x-slot:figure>
                                        @if(is_string($stat['icon']))
                                            @php
                                                $iconName = $stat['icon'];
                                            @endphp
                                            @if(str_starts_with($iconName, 'bi-'))
                                                <x-dynamic-component :component="'bi-'.str_replace('bi-', '', $iconName)" class="w-8 h-8" />
                                            @else
                                                <x-icon :name="$iconName" class="w-8 h-8" />
                                            @endif
                                        @else
                                            {!! $stat['icon'] !!}
                                        @endif
                                    </x-slot:figure>
                                @endif
                            </x-daisy::ui.data-display.stat>
                        @endforeach
                    </div>
                </x-daisy::ui.layout.crud-section>
            @endif

            {{-- Badges --}}
            @if($showBadges && count($badges) > 0)
                <x-daisy::ui.layout.crud-section
                    :title="__('profile.badges')"
                    :description="__('profile.badges_description')"
                    :borderTop="$showStats && count($stats) > 0"
                >
                    <div class="flex flex-wrap gap-2">
                        @foreach($badges as $badge)
                            <x-daisy::ui.data-display.badge
                                :color="$badge['color'] ?? 'neutral'"
                                :size="$badge['size'] ?? 'md'"
                            >
                                @if(!empty($badge['icon']))
                                    @if(is_string($badge['icon']))
                                        @php
                                            $iconName = $badge['icon'];
                                        @endphp
                                        @if(str_starts_with($iconName, 'bi-'))
                                            <x-dynamic-component :component="'bi-'.str_replace('bi-', '', $iconName)" class="w-4 h-4" />
                                        @else
                                            <x-icon :name="$iconName" class="w-4 h-4" />
                                        @endif
                                    @else
                                        {!! $badge['icon'] !!}
                                    @endif
                                @endif
                                {{ $badge['label'] ?? '' }}
                            </x-daisy::ui.data-display.badge>
                        @endforeach
                    </div>
                </x-daisy::ui.layout.crud-section>
            @endif

            {{-- Timeline --}}
            @if($showTimeline && count($timeline) > 0)
                <x-daisy::ui.layout.crud-section
                    :title="__('profile.timeline')"
                    :description="__('profile.timeline_description')"
                    :borderTop="(($showStats && count($stats) > 0) || ($showBadges && count($badges) > 0))"
                >
                    @php
                        $timelineItems = [];
                        foreach ($timeline as $item) {
                            $timelineItems[] = [
                                'when' => $item['date'] ?? '',
                                'title' => $item['title'] ?? '',
                                'content' => $item['content'] ?? null,
                                'icon' => !empty($item['icon']) ? (is_string($item['icon']) 
                                    ? (str_starts_with($item['icon'], 'bi-') 
                                        ? '<x-dynamic-component component="bi-'.str_replace('bi-', '', $item['icon']).'" class="w-5 h-5" />' 
                                        : '<x-icon name="'.$item['icon'].'" class="w-5 h-5" />')
                                    : $item['icon']) : null,
                            ];
                        }
                    @endphp
                    <x-daisy::ui.data-display.timeline :items="$timelineItems" />
                </x-daisy::ui.layout.crud-section>
            @endif

            {{-- Contact --}}
            @if($showContact && ($location || $website))
                <x-daisy::ui.layout.crud-section
                    :title="__('profile.contact')"
                    :description="__('profile.contact_description')"
                    :borderTop="(($showStats && count($stats) > 0) || ($showBadges && count($badges) > 0) || ($showTimeline && count($timeline) > 0))"
                >
                    <dl class="space-y-2">
                        @if($location)
                            <div class="flex items-center gap-2">
                                <x-bi-geo-alt class="w-5 h-5 text-base-content/50" />
                                <dt class="text-sm font-medium text-base-content/70">{{ __('profile.location') }}</dt>
                                <dd class="text-sm text-base-content">{{ $location }}</dd>
                            </div>
                        @endif
                        @if($website)
                            <div class="flex items-center gap-2">
                                <x-bi-globe class="w-5 h-5 text-base-content/50" />
                                <dt class="text-sm font-medium text-base-content/70">{{ __('profile.website') }}</dt>
                                <dd class="text-sm text-base-content">
                                    <a href="{{ $website }}" target="_blank" rel="noopener noreferrer" class="link link-hover">
                                        {{ $website }}
                                    </a>
                                </dd>
                            </div>
                        @endif
                    </dl>
                </x-daisy::ui.layout.crud-section>
            @endif
        </x-daisy::ui.layout.crud-layout>
    </div>
</x-daisy::layout.app>
