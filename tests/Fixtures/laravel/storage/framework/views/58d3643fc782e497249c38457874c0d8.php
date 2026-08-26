<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => __('daisy::profile.profile'),
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
    'title' => __('daisy::profile.profile'),
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

    $normalizeExternalUrl = function($url) {
        if (!is_string($url) && !$url instanceof \Stringable) {
            return null;
        }

        $url = trim((string) $url);

        return preg_match('/^https?:\/\//i', $url) === 1 ? $url : null;
    };

    $normalizeIconName = function($icon) {
        if (!is_string($icon) && !$icon instanceof \Stringable) {
            return null;
        }

        $icon = trim((string) $icon);

        return preg_match('/^[A-Za-z0-9_.:-]+$/', $icon) === 1 ? $icon : null;
    };

    $websiteUrl = $normalizeExternalUrl($website);

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
        ['label' => __('daisy::profile.profile'), 'href' => null],
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
        
        <div class="flex flex-col sm:flex-row gap-6 items-start sm:items-center pb-6 border-b">
            <?php if (isset($component)) { $__componentOriginalc4b515aecc51170d16885bb5fad2ac22 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc4b515aecc51170d16885bb5fad2ac22 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.avatar','data' => ['src' => $avatar,'alt' => $name,'size' => 'xl','placeholder' => mb_substr($name ?? 'A', 0, 1)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['src' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($avatar),'alt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($name),'size' => 'xl','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(mb_substr($name ?? 'A', 0, 1))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc4b515aecc51170d16885bb5fad2ac22)): ?>
<?php $attributes = $__attributesOriginalc4b515aecc51170d16885bb5fad2ac22; ?>
<?php unset($__attributesOriginalc4b515aecc51170d16885bb5fad2ac22); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc4b515aecc51170d16885bb5fad2ac22)): ?>
<?php $component = $__componentOriginalc4b515aecc51170d16885bb5fad2ac22; ?>
<?php unset($__componentOriginalc4b515aecc51170d16885bb5fad2ac22); ?>
<?php endif; ?>

            <div class="flex-1 space-y-2">
                <h1 class="text-3xl font-bold"><?php echo e($name ?? __('daisy::profile.profile')); ?></h1>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($email): ?>
                    <p class="text-base-content/70"><?php echo e($email); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bio && $showBio): ?>
                    <p class="text-base-content/80"><?php echo e($bio); ?></p>
                <?php elseif($showBio): ?>
                    <p class="text-base-content/50 italic"><?php echo e(__('daisy::profile.no_bio')); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="flex flex-wrap gap-4 text-sm text-base-content/60">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($createdAt): ?>
                        <span><?php echo e(__('daisy::profile.member_since')); ?>: <?php echo e($createdAt); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lastActive): ?>
                        <span><?php echo e(__('daisy::profile.last_active')); ?>: <?php echo e($lastActive); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOwnProfile): ?>
                <div class="flex gap-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profileEditUrl !== '#'): ?>
                        <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['tag' => 'a','href' => $profileEditUrl,'variant' => 'outline']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tag' => 'a','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profileEditUrl),'variant' => 'outline']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php echo e(__('daisy::profile.edit_profile')); ?>

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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profileSettingsUrl !== '#'): ?>
                        <?php if (isset($component)) { $__componentOriginal9a751b9910af89effffb0e7b5cd19e4e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9a751b9910af89effffb0e7b5cd19e4e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.inputs.button','data' => ['tag' => 'a','href' => $profileSettingsUrl,'variant' => 'outline']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.inputs.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['tag' => 'a','href' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($profileSettingsUrl),'variant' => 'outline']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                            <?php echo e(__('daisy::profile.settings')); ?>

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
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
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

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showStats && count($stats) > 0): ?>
                <?php if (isset($component)) { $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.crud-section','data' => ['title' => __('daisy::profile.stats'),'description' => __('daisy::profile.stats_description')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.crud-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.stats')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.stats_description'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <div class="stats stats-vertical sm:stats-horizontal shadow w-full">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginal8068cf27a7efeac75cd576054c9d9aba = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8068cf27a7efeac75cd576054c9d9aba = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.stat','data' => ['title' => $stat['label'] ?? null,'value' => $stat['value'] ?? null,'desc' => $stat['desc'] ?? null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.stat'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stat['label'] ?? null),'value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stat['value'] ?? null),'desc' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($stat['desc'] ?? null)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($stat['icon'])): ?>
                                     <?php $__env->slot('figure', null, []); ?> 
                                        <?php
                                            $iconName = $normalizeIconName($stat['icon']);
                                        ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($iconName): ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(str_starts_with($iconName, 'bi-')): ?>
                                                <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => 'bi-'.str_replace('bi-', '', $iconName)] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-8 h-8']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                                            <?php else: ?>
                                                <?php if (isset($component)) { $__componentOriginal606b6d7eddc2e418f11096356be15e19 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal606b6d7eddc2e418f11096356be15e19 = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Icon::resolve(['name' => $iconName] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-8 h-8']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal606b6d7eddc2e418f11096356be15e19)): ?>
<?php $attributes = $__attributesOriginal606b6d7eddc2e418f11096356be15e19; ?>
<?php unset($__attributesOriginal606b6d7eddc2e418f11096356be15e19); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal606b6d7eddc2e418f11096356be15e19)): ?>
<?php $component = $__componentOriginal606b6d7eddc2e418f11096356be15e19; ?>
<?php unset($__componentOriginal606b6d7eddc2e418f11096356be15e19); ?>
<?php endif; ?>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php elseif($stat['icon'] instanceof \Illuminate\Contracts\Support\Htmlable): ?>
                                            <?php echo $stat['icon']->toHtml(); ?>

                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                     <?php $__env->endSlot(); ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8068cf27a7efeac75cd576054c9d9aba)): ?>
<?php $attributes = $__attributesOriginal8068cf27a7efeac75cd576054c9d9aba; ?>
<?php unset($__attributesOriginal8068cf27a7efeac75cd576054c9d9aba); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8068cf27a7efeac75cd576054c9d9aba)): ?>
<?php $component = $__componentOriginal8068cf27a7efeac75cd576054c9d9aba; ?>
<?php unset($__componentOriginal8068cf27a7efeac75cd576054c9d9aba); ?>
<?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
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

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showBadges && count($badges) > 0): ?>
                <?php if (isset($component)) { $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.crud-section','data' => ['title' => __('daisy::profile.badges'),'description' => __('daisy::profile.badges_description'),'borderTop' => $showStats && count($stats) > 0]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.crud-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.badges')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.badges_description')),'borderTop' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($showStats && count($stats) > 0)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <div class="flex flex-wrap gap-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $badges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $badge): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginalda6138eea77c89c66aa47df8543151c4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalda6138eea77c89c66aa47df8543151c4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.badge','data' => ['color' => $badge['color'] ?? 'neutral','size' => $badge['size'] ?? 'md']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($badge['color'] ?? 'neutral'),'size' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($badge['size'] ?? 'md')]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($badge['icon'])): ?>
                                    <?php
                                        $iconName = $normalizeIconName($badge['icon']);
                                    ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($iconName): ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(str_starts_with($iconName, 'bi-')): ?>
                                            <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => 'bi-'.str_replace('bi-', '', $iconName)] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                                        <?php else: ?>
                                            <?php if (isset($component)) { $__componentOriginal606b6d7eddc2e418f11096356be15e19 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal606b6d7eddc2e418f11096356be15e19 = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Icon::resolve(['name' => $iconName] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal606b6d7eddc2e418f11096356be15e19)): ?>
<?php $attributes = $__attributesOriginal606b6d7eddc2e418f11096356be15e19; ?>
<?php unset($__attributesOriginal606b6d7eddc2e418f11096356be15e19); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal606b6d7eddc2e418f11096356be15e19)): ?>
<?php $component = $__componentOriginal606b6d7eddc2e418f11096356be15e19; ?>
<?php unset($__componentOriginal606b6d7eddc2e418f11096356be15e19); ?>
<?php endif; ?>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php elseif($badge['icon'] instanceof \Illuminate\Contracts\Support\Htmlable): ?>
                                        <?php echo $badge['icon']->toHtml(); ?>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php echo e($badge['label'] ?? ''); ?>

                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalda6138eea77c89c66aa47df8543151c4)): ?>
<?php $attributes = $__attributesOriginalda6138eea77c89c66aa47df8543151c4; ?>
<?php unset($__attributesOriginalda6138eea77c89c66aa47df8543151c4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalda6138eea77c89c66aa47df8543151c4)): ?>
<?php $component = $__componentOriginalda6138eea77c89c66aa47df8543151c4; ?>
<?php unset($__componentOriginalda6138eea77c89c66aa47df8543151c4); ?>
<?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
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

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showTimeline && count($timeline) > 0): ?>
                <?php if (isset($component)) { $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.crud-section','data' => ['title' => __('daisy::profile.timeline'),'description' => __('daisy::profile.timeline_description'),'borderTop' => (($showStats && count($stats) > 0) || ($showBadges && count($badges) > 0))]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.crud-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.timeline')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.timeline_description')),'borderTop' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((($showStats && count($stats) > 0) || ($showBadges && count($badges) > 0)))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <?php
                        $timelineItems = [];
                        foreach ($timeline as $item) {
                            $icon = $item['icon'] ?? null;
                            $iconName = $normalizeIconName($icon);
                            $timelineItems[] = [
                                'when' => $item['date'] ?? '',
                                'title' => $item['title'] ?? '',
                                'content' => $item['content'] ?? null,
                                'iconName' => $iconName,
                                'icon' => $iconName ? null : $icon,
                            ];
                        }
                    ?>
                    <?php if (isset($component)) { $__componentOriginalcc92d245e1a8db225414555b30e0f790 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcc92d245e1a8db225414555b30e0f790 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.data-display.timeline','data' => ['items' => $timelineItems]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.data-display.timeline'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($timelineItems)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcc92d245e1a8db225414555b30e0f790)): ?>
<?php $attributes = $__attributesOriginalcc92d245e1a8db225414555b30e0f790; ?>
<?php unset($__attributesOriginalcc92d245e1a8db225414555b30e0f790); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcc92d245e1a8db225414555b30e0f790)): ?>
<?php $component = $__componentOriginalcc92d245e1a8db225414555b30e0f790; ?>
<?php unset($__componentOriginalcc92d245e1a8db225414555b30e0f790); ?>
<?php endif; ?>
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

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showContact && ($location || $website)): ?>
                <?php if (isset($component)) { $__componentOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3ac2fdb67b83cf2c34a1b1c2e1cd3974 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.crud-section','data' => ['title' => __('daisy::profile.contact'),'description' => __('daisy::profile.contact_description'),'borderTop' => (($showStats && count($stats) > 0) || ($showBadges && count($badges) > 0) || ($showTimeline && count($timeline) > 0))]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.crud-section'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.contact')),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('daisy::profile.contact_description')),'borderTop' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute((($showStats && count($stats) > 0) || ($showBadges && count($badges) > 0) || ($showTimeline && count($timeline) > 0)))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                    <dl class="space-y-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($location): ?>
                            <div class="flex items-center gap-2">
                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bi-geo-alt'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-base-content/50']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                <dt class="text-sm font-medium text-base-content/70"><?php echo e(__('daisy::profile.location')); ?></dt>
                                <dd class="text-sm text-base-content"><?php echo e($location); ?></dd>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($website): ?>
                            <div class="flex items-center gap-2">
                                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('bi-globe'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 text-base-content/50']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                <dt class="text-sm font-medium text-base-content/70"><?php echo e(__('daisy::profile.website')); ?></dt>
                                <dd class="text-sm text-base-content">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($websiteUrl): ?>
                                        <a href="<?php echo e($websiteUrl); ?>" target="_blank" rel="noopener noreferrer" class="link link-hover">
                                            <?php echo e($website); ?>

                                        </a>
                                    <?php else: ?>
                                        <?php echo e($website); ?>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </dd>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/templates/profile/profile-view.blade.php ENDPATH**/ ?>