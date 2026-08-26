<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => __('daisy::profile.settings'),
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
    'availableLanguages' => null,
    'availableTimezones' => null,
    'availableThemes' => null,
    // Readonly mode
    'readonly' => false,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'title' => __('daisy::profile.settings'),
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
    'availableLanguages' => null,
    'availableTimezones' => null,
    'availableThemes' => null,
    // Readonly mode
    'readonly' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
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

    $normalizeFormAction = function($url) {
        if (!is_string($url) && !$url instanceof \Stringable) {
            return '#';
        }

        $url = trim((string) $url);

        if ($url === '') {
            return '#';
        }

        if ($url === '#' || str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return $url;
        }

        return preg_match('/^https?:\/\//i', $url) === 1 ? $url : '#';
    };

    $action = $normalizeFormAction($action);

    // Build breadcrumbs
    $breadcrumbs = [
        ['label' => __('daisy::profile.profile'), 'href' => $profileViewUrl !== '#' ? $profileViewUrl : null],
        ['label' => __('daisy::profile.settings'), 'href' => null],
    ];

    // Determine HTTP method for form
    $httpMethod = strtoupper($method);
    $needsMethodOverride = in_array($httpMethod, ['PUT', 'PATCH', 'DELETE']);

    // Available options can be provided by the host app, or resolved from sensible package defaults.
    $availableLanguages ??= config('app.locales', ['fr' => 'Français', 'en' => 'English']);

    $availableTimezones ??= [
        'UTC' => 'UTC',
        'Europe/Paris' => 'Europe/Paris (CET)',
        'Europe/London' => 'Europe/London (GMT)',
        'America/New_York' => 'America/New_York (EST)',
        'America/Los_Angeles' => 'America/Los_Angeles (PST)',
        'Asia/Tokyo' => 'Asia/Tokyo (JST)',
    ];

    $availableThemes ??= [
        'light' => __('daisy::profile.theme_light'),
        'dark' => __('daisy::profile.theme_dark'),
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
?>

<?php if (isset($component)) { $__componentOriginala7bea3f816103b034498a0cafca82f36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala7bea3f816103b034498a0cafca82f36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.layout.app','data' => ['title' => $title,'theme' => $theme,'container' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::layout.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title),'theme' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($theme),'container' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php if (isset($component)) { $__componentOriginald4aaa4b01baa94db46e38e4697384b0c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4aaa4b01baa94db46e38e4697384b0c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.theme-selector','data' => ['position' => 'fixed','placement' => 'top-right']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.theme-selector'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['position' => 'fixed','placement' => 'top-right']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4aaa4b01baa94db46e38e4697384b0c)): ?>
<?php $attributes = $__attributesOriginald4aaa4b01baa94db46e38e4697384b0c; ?>
<?php unset($__attributesOriginald4aaa4b01baa94db46e38e4697384b0c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4aaa4b01baa94db46e38e4697384b0c)): ?>
<?php $component = $__componentOriginald4aaa4b01baa94db46e38e4697384b0c; ?>
<?php unset($__componentOriginald4aaa4b01baa94db46e38e4697384b0c); ?>
<?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($header)): ?>
        <?php echo e($header); ?>

    <?php else: ?>
        <?php if (isset($component)) { $__componentOriginale581b8b2ca5d662c3ffd0868f07f81bf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale581b8b2ca5d662c3ffd0868f07f81bf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.navigation.breadcrumbs','data' => ['items' => $breadcrumbs]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.navigation.breadcrumbs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($breadcrumbs)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale581b8b2ca5d662c3ffd0868f07f81bf)): ?>
<?php $attributes = $__attributesOriginale581b8b2ca5d662c3ffd0868f07f81bf; ?>
<?php unset($__attributesOriginale581b8b2ca5d662c3ffd0868f07f81bf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale581b8b2ca5d662c3ffd0868f07f81bf)): ?>
<?php $component = $__componentOriginale581b8b2ca5d662c3ffd0868f07f81bf; ?>
<?php unset($__componentOriginale581b8b2ca5d662c3ffd0868f07f81bf); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="space-y-6">
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('status')): ?>
            <?php if (isset($component)) { $__componentOriginalc4cebe93f4bb6cb8648bf0957d149152 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc4cebe93f4bb6cb8648bf0957d149152 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.feedback.alert','data' => ['color' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.feedback.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'success']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php echo e(session('status')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc4cebe93f4bb6cb8648bf0957d149152)): ?>
<?php $attributes = $__attributesOriginalc4cebe93f4bb6cb8648bf0957d149152; ?>
<?php unset($__attributesOriginalc4cebe93f4bb6cb8648bf0957d149152); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc4cebe93f4bb6cb8648bf0957d149152)): ?>
<?php $component = $__componentOriginalc4cebe93f4bb6cb8648bf0957d149152; ?>
<?php unset($__componentOriginalc4cebe93f4bb6cb8648bf0957d149152); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <?php if (isset($component)) { $__componentOriginalc4cebe93f4bb6cb8648bf0957d149152 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc4cebe93f4bb6cb8648bf0957d149152 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.feedback.alert','data' => ['color' => 'error']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.feedback.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'error']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <ul class="list-disc list-inside">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <li><?php echo e($error); ?></li>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </ul>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc4cebe93f4bb6cb8648bf0957d149152)): ?>
<?php $attributes = $__attributesOriginalc4cebe93f4bb6cb8648bf0957d149152; ?>
<?php unset($__attributesOriginalc4cebe93f4bb6cb8648bf0957d149152); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc4cebe93f4bb6cb8648bf0957d149152)): ?>
<?php $component = $__componentOriginalc4cebe93f4bb6cb8648bf0957d149152; ?>
<?php unset($__componentOriginalc4cebe93f4bb6cb8648bf0957d149152); ?>
<?php endif; ?>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($readonly): ?>
            
            <?php if (isset($component)) { $__componentOriginalbe69f52a68d3708f38c4da18c7056e41 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbe69f52a68d3708f38c4da18c7056e41 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.crud-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.crud-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showPreferences): ?>
                    <?php if (isset($component)) { $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.crud-section','data' => ['title' => __('daisy::profile.preferences'),'description' => __('daisy::profile.preferences_description')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.crud-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.preferences')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.preferences_description'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-base-content/70"><?php echo e(__('daisy::profile.language')); ?></dt>
                                <dd class="mt-1 text-sm text-base-content"><?php echo e($availableLanguages[$language] ?? $language); ?></dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-base-content/70"><?php echo e(__('daisy::profile.timezone')); ?></dt>
                                <dd class="mt-1 text-sm text-base-content"><?php echo e($availableTimezones[$timezone] ?? $timezone); ?></dd>
                            </div>
                        </dl>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974)): ?>
<?php $attributes = $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974; ?>
<?php unset($__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974)): ?>
<?php $component = $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974; ?>
<?php unset($__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showNotifications): ?>
                    <?php if (isset($component)) { $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.crud-section','data' => ['title' => __('daisy::profile.notifications'),'description' => __('daisy::profile.notifications_description'),'borderTop' => $showPreferences]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.crud-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.notifications')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.notifications_description')),'borderTop' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showPreferences)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-base-content/70"><?php echo e(__('daisy::profile.email_notifications')); ?></dt>
                                <dd class="mt-1 text-sm text-base-content"><?php echo e($notifyEmail ? __('Yes') : __('No')); ?></dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-base-content/70"><?php echo e(__('daisy::profile.push_notifications')); ?></dt>
                                <dd class="mt-1 text-sm text-base-content"><?php echo e($notifyPush ? __('Yes') : __('No')); ?></dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-base-content/70"><?php echo e(__('daisy::profile.sms_notifications')); ?></dt>
                                <dd class="mt-1 text-sm text-base-content"><?php echo e($notifySms ? __('Yes') : __('No')); ?></dd>
                            </div>
                        </dl>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974)): ?>
<?php $attributes = $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974; ?>
<?php unset($__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974)): ?>
<?php $component = $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974; ?>
<?php unset($__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showSecurity): ?>
                    <?php if (isset($component)) { $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.crud-section','data' => ['title' => __('daisy::profile.security'),'description' => __('daisy::profile.security_description'),'borderTop' => ($showPreferences || $showNotifications)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.crud-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.security')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.security_description')),'borderTop' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($showPreferences || $showNotifications))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-base-content/70"><?php echo e(__('daisy::profile.two_factor_auth')); ?></dt>
                                <dd class="mt-1 text-sm text-base-content"><?php echo e($twoFactorEnabled ? __('Yes') : __('No')); ?></dd>
                            </div>
                        </dl>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974)): ?>
<?php $attributes = $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974; ?>
<?php unset($__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974)): ?>
<?php $component = $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974; ?>
<?php unset($__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showTheme): ?>
                    <?php if (isset($component)) { $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.crud-section','data' => ['title' => __('daisy::profile.appearance'),'description' => __('daisy::profile.appearance_description'),'borderTop' => ($showPreferences || $showNotifications || $showSecurity)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.crud-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.appearance')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.appearance_description')),'borderTop' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($showPreferences || $showNotifications || $showSecurity))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-base-content/70"><?php echo e(__('daisy::profile.theme')); ?></dt>
                                <dd class="mt-1 text-sm text-base-content"><?php echo e(ucfirst(str_replace('_', ' ', $currentTheme))); ?></dd>
                            </div>
                        </dl>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974)): ?>
<?php $attributes = $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974; ?>
<?php unset($__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974)): ?>
<?php $component = $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974; ?>
<?php unset($__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974); ?>
<?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbe69f52a68d3708f38c4da18c7056e41)): ?>
<?php $attributes = $__attributesOriginalbe69f52a68d3708f38c4da18c7056e41; ?>
<?php unset($__attributesOriginalbe69f52a68d3708f38c4da18c7056e41); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbe69f52a68d3708f38c4da18c7056e41)): ?>
<?php $component = $__componentOriginalbe69f52a68d3708f38c4da18c7056e41; ?>
<?php unset($__componentOriginalbe69f52a68d3708f38c4da18c7056e41); ?>
<?php endif; ?>
        <?php else: ?>
            
            <form action="<?php echo e($action); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($needsMethodOverride): ?>
                    <?php echo method_field($httpMethod); ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if (isset($component)) { $__componentOriginalbe69f52a68d3708f38c4da18c7056e41 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbe69f52a68d3708f38c4da18c7056e41 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.crud-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.crud-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showPreferences): ?>
                        <?php if (isset($component)) { $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.crud-section','data' => ['title' => __('daisy::profile.preferences'),'description' => __('daisy::profile.preferences_description')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.crud-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.preferences')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.preferences_description'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <div class="space-y-4">
                                <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['name' => 'language','label' => __('daisy::profile.language'),'required' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'language','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.language')),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                    <?php if (isset($component)) { $__componentOriginale3f19de9d041234399138af8d6d623fc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3f19de9d041234399138af8d6d623fc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.select','data' => ['name' => 'language','class' => $errors->has('language') ? 'select-error' : '']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'language','class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->has('language') ? 'select-error' : '')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $availableLanguages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <option value="<?php echo e($code); ?>" <?php if($language === $code): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3f19de9d041234399138af8d6d623fc)): ?>
<?php $attributes = $__attributesOriginale3f19de9d041234399138af8d6d623fc; ?>
<?php unset($__attributesOriginale3f19de9d041234399138af8d6d623fc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3f19de9d041234399138af8d6d623fc)): ?>
<?php $component = $__componentOriginale3f19de9d041234399138af8d6d623fc; ?>
<?php unset($__componentOriginale3f19de9d041234399138af8d6d623fc); ?>
<?php endif; ?>
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $attributes = $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $component = $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>

                                <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['name' => 'timezone','label' => __('daisy::profile.timezone'),'required' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'timezone','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.timezone')),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                    <?php if (isset($component)) { $__componentOriginale3f19de9d041234399138af8d6d623fc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3f19de9d041234399138af8d6d623fc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.select','data' => ['name' => 'timezone','class' => $errors->has('timezone') ? 'select-error' : '']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'timezone','class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->has('timezone') ? 'select-error' : '')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $availableTimezones; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <option value="<?php echo e($code); ?>" <?php if($timezone === $code): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3f19de9d041234399138af8d6d623fc)): ?>
<?php $attributes = $__attributesOriginale3f19de9d041234399138af8d6d623fc; ?>
<?php unset($__attributesOriginale3f19de9d041234399138af8d6d623fc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3f19de9d041234399138af8d6d623fc)): ?>
<?php $component = $__componentOriginale3f19de9d041234399138af8d6d623fc; ?>
<?php unset($__componentOriginale3f19de9d041234399138af8d6d623fc); ?>
<?php endif; ?>
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $attributes = $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $component = $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
                            </div>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974)): ?>
<?php $attributes = $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974; ?>
<?php unset($__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974)): ?>
<?php $component = $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974; ?>
<?php unset($__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showNotifications): ?>
                        <?php if (isset($component)) { $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.crud-section','data' => ['title' => __('daisy::profile.notifications'),'description' => __('daisy::profile.notifications_description'),'borderTop' => $showPreferences]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.crud-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.notifications')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.notifications_description')),'borderTop' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showPreferences)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <div class="space-y-6">
                                <div class="space-y-4">
                                    <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['name' => 'notify_email','label' => __('daisy::profile.email_notifications'),'required' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'notify_email','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.email_notifications')),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                        <div class="flex items-center gap-2">
                                            <?php if (isset($component)) { $__componentOriginal5e0e650e8e3e72833de5c0990cf927b2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.toggle','data' => ['name' => 'notify_email','checked' => $notifyEmail]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.toggle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'notify_email','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notifyEmail)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2)): ?>
<?php $attributes = $__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2; ?>
<?php unset($__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5e0e650e8e3e72833de5c0990cf927b2)): ?>
<?php $component = $__componentOriginal5e0e650e8e3e72833de5c0990cf927b2; ?>
<?php unset($__componentOriginal5e0e650e8e3e72833de5c0990cf927b2); ?>
<?php endif; ?>
                                            <span class="text-sm text-base-content/70"><?php echo e(__('daisy::profile.receive_notifications_by_email')); ?></span>
                                        </div>
                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $attributes = $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $component = $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>

                                    <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['name' => 'notify_push','label' => __('daisy::profile.push_notifications'),'required' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'notify_push','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.push_notifications')),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                        <div class="flex items-center gap-2">
                                            <?php if (isset($component)) { $__componentOriginal5e0e650e8e3e72833de5c0990cf927b2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.toggle','data' => ['name' => 'notify_push','checked' => $notifyPush]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.toggle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'notify_push','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notifyPush)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2)): ?>
<?php $attributes = $__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2; ?>
<?php unset($__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5e0e650e8e3e72833de5c0990cf927b2)): ?>
<?php $component = $__componentOriginal5e0e650e8e3e72833de5c0990cf927b2; ?>
<?php unset($__componentOriginal5e0e650e8e3e72833de5c0990cf927b2); ?>
<?php endif; ?>
                                            <span class="text-sm text-base-content/70"><?php echo e(__('daisy::profile.receive_push_notifications')); ?></span>
                                        </div>
                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $attributes = $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $component = $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>

                                    <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['name' => 'notify_sms','label' => __('daisy::profile.sms_notifications'),'required' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'notify_sms','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.sms_notifications')),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                        <div class="flex items-center gap-2">
                                            <?php if (isset($component)) { $__componentOriginal5e0e650e8e3e72833de5c0990cf927b2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.toggle','data' => ['name' => 'notify_sms','checked' => $notifySms]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.toggle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'notify_sms','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notifySms)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2)): ?>
<?php $attributes = $__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2; ?>
<?php unset($__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5e0e650e8e3e72833de5c0990cf927b2)): ?>
<?php $component = $__componentOriginal5e0e650e8e3e72833de5c0990cf927b2; ?>
<?php unset($__componentOriginal5e0e650e8e3e72833de5c0990cf927b2); ?>
<?php endif; ?>
                                            <span class="text-sm text-base-content/70"><?php echo e(__('daisy::profile.receive_sms_notifications')); ?></span>
                                        </div>
                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $attributes = $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $component = $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
                                </div>

                                <div class="divider"></div>

                                <div class="space-y-3">
                                    <h3 class="text-sm font-medium"><?php echo e(__('daisy::profile.notification_types')); ?></h3>
                                    <div class="space-y-2">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <?php if (isset($component)) { $__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.checkbox','data' => ['name' => 'notify_features','checked' => $notifyFeatures]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'notify_features','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notifyFeatures)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf)): ?>
<?php $attributes = $__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf; ?>
<?php unset($__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf)): ?>
<?php $component = $__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf; ?>
<?php unset($__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf); ?>
<?php endif; ?>
                                            <span class="text-sm"><?php echo e(__('daisy::profile.new_features')); ?></span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <?php if (isset($component)) { $__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.checkbox','data' => ['name' => 'notify_messages','checked' => $notifyMessages]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'notify_messages','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notifyMessages)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf)): ?>
<?php $attributes = $__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf; ?>
<?php unset($__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf)): ?>
<?php $component = $__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf; ?>
<?php unset($__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf); ?>
<?php endif; ?>
                                            <span class="text-sm"><?php echo e(__('daisy::profile.messages')); ?></span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <?php if (isset($component)) { $__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.checkbox','data' => ['name' => 'notify_comments','checked' => $notifyComments]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'notify_comments','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notifyComments)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf)): ?>
<?php $attributes = $__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf; ?>
<?php unset($__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf)): ?>
<?php $component = $__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf; ?>
<?php unset($__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf); ?>
<?php endif; ?>
                                            <span class="text-sm"><?php echo e(__('daisy::profile.comments')); ?></span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <?php if (isset($component)) { $__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.checkbox','data' => ['name' => 'notify_mentions','checked' => $notifyMentions]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.checkbox'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'notify_mentions','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($notifyMentions)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf)): ?>
<?php $attributes = $__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf; ?>
<?php unset($__attributesOriginal34520fed9d95c031e4a00dbf3f0eaddf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf)): ?>
<?php $component = $__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf; ?>
<?php unset($__componentOriginal34520fed9d95c031e4a00dbf3f0eaddf); ?>
<?php endif; ?>
                                            <span class="text-sm"><?php echo e(__('daisy::profile.mentions')); ?></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974)): ?>
<?php $attributes = $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974; ?>
<?php unset($__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974)): ?>
<?php $component = $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974; ?>
<?php unset($__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showSecurity): ?>
                        <?php if (isset($component)) { $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.crud-section','data' => ['title' => __('daisy::profile.security'),'description' => __('daisy::profile.security_description'),'borderTop' => ($showPreferences || $showNotifications)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.crud-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.security')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.security_description')),'borderTop' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($showPreferences || $showNotifications))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <div class="space-y-6">
                                <?php if (isset($component)) { $__componentOriginal274b9efc705e2ca14de4bca21cbf7946 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal274b9efc705e2ca14de4bca21cbf7946 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.collapse','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.collapse'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                     <?php $__env->slot('title', null, []); ?> 
                                        <span class="font-semibold"><?php echo e(__('daisy::profile.change_password')); ?></span>
                                     <?php $__env->endSlot(); ?>
                                    <div class="space-y-4 pt-2">
                                        <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['name' => 'current_password','label' => __('daisy::profile.current_password'),'required' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'current_password','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.current_password')),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                            <?php if (isset($component)) { $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.input','data' => ['name' => 'current_password','type' => 'password','autocomplete' => 'current-password','class' => $errors->has('current_password') ? 'input-error' : '']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'current_password','type' => 'password','autocomplete' => 'current-password','class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->has('current_password') ? 'input-error' : '')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b)): ?>
<?php $attributes = $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b; ?>
<?php unset($__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b)): ?>
<?php $component = $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b; ?>
<?php unset($__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b); ?>
<?php endif; ?>
                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $attributes = $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $component = $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>

                                        <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['name' => 'password','label' => __('daisy::profile.new_password'),'required' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'password','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.new_password')),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                            <?php if (isset($component)) { $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.input','data' => ['name' => 'password','type' => 'password','autocomplete' => 'new-password','class' => $errors->has('password') ? 'input-error' : '']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'password','type' => 'password','autocomplete' => 'new-password','class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->has('password') ? 'input-error' : '')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b)): ?>
<?php $attributes = $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b; ?>
<?php unset($__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b)): ?>
<?php $component = $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b; ?>
<?php unset($__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b); ?>
<?php endif; ?>
                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $attributes = $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $component = $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>

                                        <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['name' => 'password_confirmation','label' => __('daisy::profile.confirm_new_password'),'required' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'password_confirmation','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.confirm_new_password')),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                            <?php if (isset($component)) { $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.input','data' => ['name' => 'password_confirmation','type' => 'password','autocomplete' => 'new-password','class' => $errors->has('password_confirmation') ? 'input-error' : '']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'password_confirmation','type' => 'password','autocomplete' => 'new-password','class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->has('password_confirmation') ? 'input-error' : '')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b)): ?>
<?php $attributes = $__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b; ?>
<?php unset($__attributesOriginal10aa88a1ed6711dfa01f4832ab67a57b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b)): ?>
<?php $component = $__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b; ?>
<?php unset($__componentOriginal10aa88a1ed6711dfa01f4832ab67a57b); ?>
<?php endif; ?>
                                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $attributes = $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $component = $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
                                    </div>
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal274b9efc705e2ca14de4bca21cbf7946)): ?>
<?php $attributes = $__attributesOriginal274b9efc705e2ca14de4bca21cbf7946; ?>
<?php unset($__attributesOriginal274b9efc705e2ca14de4bca21cbf7946); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal274b9efc705e2ca14de4bca21cbf7946)): ?>
<?php $component = $__componentOriginal274b9efc705e2ca14de4bca21cbf7946; ?>
<?php unset($__componentOriginal274b9efc705e2ca14de4bca21cbf7946); ?>
<?php endif; ?>

                                <div class="divider"></div>

                                <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['name' => 'two_factor_enabled','label' => __('daisy::profile.two_factor_auth'),'required' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'two_factor_enabled','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.two_factor_auth')),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                    <div class="flex items-center gap-2">
                                        <?php if (isset($component)) { $__componentOriginal5e0e650e8e3e72833de5c0990cf927b2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.toggle','data' => ['name' => 'two_factor_enabled','checked' => $twoFactorEnabled]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.toggle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'two_factor_enabled','checked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($twoFactorEnabled)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2)): ?>
<?php $attributes = $__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2; ?>
<?php unset($__attributesOriginal5e0e650e8e3e72833de5c0990cf927b2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5e0e650e8e3e72833de5c0990cf927b2)): ?>
<?php $component = $__componentOriginal5e0e650e8e3e72833de5c0990cf927b2; ?>
<?php unset($__componentOriginal5e0e650e8e3e72833de5c0990cf927b2); ?>
<?php endif; ?>
                                        <span class="text-sm text-base-content/70"><?php echo e(__('daisy::profile.enable_two_factor_authentication')); ?></span>
                                    </div>
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $attributes = $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $component = $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
                            </div>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974)): ?>
<?php $attributes = $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974; ?>
<?php unset($__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974)): ?>
<?php $component = $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974; ?>
<?php unset($__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showPrivacy): ?>
                        <?php if (isset($component)) { $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.crud-section','data' => ['title' => __('daisy::profile.privacy'),'description' => __('daisy::profile.privacy_description'),'borderTop' => ($showPreferences || $showNotifications || $showSecurity)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.crud-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.privacy')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.privacy_description')),'borderTop' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($showPreferences || $showNotifications || $showSecurity))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <p class="text-base-content/70"><?php echo e(__('daisy::profile.coming_soon')); ?></p>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974)): ?>
<?php $attributes = $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974; ?>
<?php unset($__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974)): ?>
<?php $component = $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974; ?>
<?php unset($__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showTheme): ?>
                        <?php if (isset($component)) { $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.crud-section','data' => ['title' => __('daisy::profile.appearance'),'description' => __('daisy::profile.appearance_description'),'borderTop' => ($showPreferences || $showNotifications || $showSecurity || $showPrivacy)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.crud-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.appearance')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.appearance_description')),'borderTop' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($showPreferences || $showNotifications || $showSecurity || $showPrivacy))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <div class="space-y-4">
                                <?php if (isset($component)) { $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.partials.form-field','data' => ['name' => 'theme','label' => __('daisy::profile.theme'),'required' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.partials.form-field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'theme','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.theme')),'required' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                    <?php if (isset($component)) { $__componentOriginale3f19de9d041234399138af8d6d623fc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale3f19de9d041234399138af8d6d623fc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.select','data' => ['name' => 'theme','class' => $errors->has('theme') ? 'select-error' : '']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'theme','class' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->has('theme') ? 'select-error' : '')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $availableThemes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $themeValue => $themeLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <option value="<?php echo e($themeValue); ?>" <?php if($currentTheme === $themeValue): echo 'selected'; endif; ?>><?php echo e($themeLabel); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale3f19de9d041234399138af8d6d623fc)): ?>
<?php $attributes = $__attributesOriginale3f19de9d041234399138af8d6d623fc; ?>
<?php unset($__attributesOriginale3f19de9d041234399138af8d6d623fc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale3f19de9d041234399138af8d6d623fc)): ?>
<?php $component = $__componentOriginale3f19de9d041234399138af8d6d623fc; ?>
<?php unset($__componentOriginale3f19de9d041234399138af8d6d623fc); ?>
<?php endif; ?>
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $attributes = $__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__attributesOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc)): ?>
<?php $component = $__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc; ?>
<?php unset($__componentOriginalc5dfd1be562d09d3d23e6322f4d92ecc); ?>
<?php endif; ?>
                            </div>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974)): ?>
<?php $attributes = $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974; ?>
<?php unset($__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974)): ?>
<?php $component = $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974; ?>
<?php unset($__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974); ?>
<?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                     <?php $__env->slot('actions', null, []); ?> 
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profileViewUrl !== '#'): ?>
                            <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['tag' => 'a','href' => $profileViewUrl,'variant' => 'outline']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tag' => 'a','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profileViewUrl),'variant' => 'outline']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <?php echo e(__('daisy::profile.cancel')); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['type' => 'submit','variant' => 'solid']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'solid']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php echo e(__('daisy::profile.save')); ?>

                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $attributes = $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e)): ?>
<?php $component = $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e; ?>
<?php unset($__componentOriginal9a751b9910af89effffb0e7b5cd19e4e); ?>
<?php endif; ?>
                     <?php $__env->endSlot(); ?>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbe69f52a68d3708f38c4da18c7056e41)): ?>
<?php $attributes = $__attributesOriginalbe69f52a68d3708f38c4da18c7056e41; ?>
<?php unset($__attributesOriginalbe69f52a68d3708f38c4da18c7056e41); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbe69f52a68d3708f38c4da18c7056e41)): ?>
<?php $component = $__componentOriginalbe69f52a68d3708f38c4da18c7056e41; ?>
<?php unset($__componentOriginalbe69f52a68d3708f38c4da18c7056e41); ?>
<?php endif; ?>
            </form>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala7bea3f816103b034498a0cafca82f36)): ?>
<?php $attributes = $__attributesOriginala7bea3f816103b034498a0cafca82f36; ?>
<?php unset($__attributesOriginala7bea3f816103b034498a0cafca82f36); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala7bea3f816103b034498a0cafca82f36)): ?>
<?php $component = $__componentOriginala7bea3f816103b034498a0cafca82f36; ?>
<?php unset($__componentOriginala7bea3f816103b034498a0cafca82f36); ?>
<?php endif; ?>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views//templates/profile/profile-settings.blade.php ENDPATH**/ ?>