<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    // Style général
    'bg' => 'base-200',
    'text' => 'base-content',
    'padding' => 'p-10',
    // Layout
    'center' => false,
    'horizontal' => false,
    'horizontalAt' => null, // sm|md|lg|xl → sm:footer-horizontal
    // Grid layout
    'gap' => 6, // gap pour grid-layout
    // Colonnes de navigation
    'columns' => [], // [{ title: string, links: [{ label: string, href: string, external?: bool }] }]
    // Branding
    'logo' => null, // string (URL) ou slot 'logo'
    'brandText' => null, // string
    'brandDescription' => null, // string
    // Copyright
    'copyright' => null, // string ou slot 'copyright'
    'copyrightYear' => null, // int (auto: année actuelle)
    'copyrightText' => null, // string
    // Réseaux sociaux
    'socialLinks' => [], // [{ icon: string, href: string, label?: string, external?: bool }]
    // Newsletter
    'newsletter' => false,
    'newsletterTitle' => null,
    'newsletterDescription' => null,
    'newsletterAction' => null, // string (URL)
    'newsletterMethod' => 'POST',
    // Divider
    'showDivider' => true,
    'dividerColor' => null, // null = auto selon bg
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
    // Style général
    'bg' => 'base-200',
    'text' => 'base-content',
    'padding' => 'p-10',
    // Layout
    'center' => false,
    'horizontal' => false,
    'horizontalAt' => null, // sm|md|lg|xl → sm:footer-horizontal
    // Grid layout
    'gap' => 6, // gap pour grid-layout
    // Colonnes de navigation
    'columns' => [], // [{ title: string, links: [{ label: string, href: string, external?: bool }] }]
    // Branding
    'logo' => null, // string (URL) ou slot 'logo'
    'brandText' => null, // string
    'brandDescription' => null, // string
    // Copyright
    'copyright' => null, // string ou slot 'copyright'
    'copyrightYear' => null, // int (auto: année actuelle)
    'copyrightText' => null, // string
    // Réseaux sociaux
    'socialLinks' => [], // [{ icon: string, href: string, label?: string, external?: bool }]
    // Newsletter
    'newsletter' => false,
    'newsletterTitle' => null,
    'newsletterDescription' => null,
    'newsletterAction' => null, // string (URL)
    'newsletterMethod' => 'POST',
    // Divider
    'showDivider' => true,
    'dividerColor' => null, // null = auto selon bg
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    // Classes du footer
    $footerClasses = 'footer';
    if ($center) {
        $footerClasses .= ' footer-center';
    }
    if ($horizontal) {
        $footerClasses .= ' footer-horizontal';
    }
    if ($horizontalAt) {
        $footerClasses .= ' '.$horizontalAt.':footer-horizontal';
    }
    if ($bg) {
        $footerClasses .= ' bg-'.$bg;
    }
    if ($text) {
        $footerClasses .= ' text-'.$text;
    }
    if ($padding) {
        $footerClasses .= ' '.$padding;
    }

    // Année du copyright
    $year = $copyrightYear ?? date('Y');

    // Divider color auto
    if ($showDivider && $dividerColor === null) {
        $dividerColor = match($bg) {
            'base-100' => 'base-300',
            'base-200' => 'base-300',
            'base-300' => 'base-content',
            default => 'base-300',
        };
    }

    $normalizeHref = function($url) {
        if (!is_string($url) && !$url instanceof \Stringable) {
            return '#';
        }

        $url = trim((string) $url);

        if ($url === '' || $url === '#') {
            return '#';
        }

        if (str_starts_with($url, '/') || str_starts_with($url, '#')) {
            return $url;
        }

        return preg_match('/^(https?:|mailto:|tel:)/i', $url) === 1 ? $url : '#';
    };

    $newsletterFormMethod = strtoupper($newsletterMethod);
    $newsletterHtmlMethod = $newsletterFormMethod === 'GET' ? 'GET' : 'POST';

    $normalizeFormAction = function($url) {
        if (!is_string($url) && !$url instanceof \Stringable) {
            return null;
        }

        $url = trim((string) $url);

        if ($url === '' || $url === '#') {
            return null;
        }

        if (str_starts_with($url, '/')) {
            return $url;
        }

        return preg_match('/^https?:\/\//i', $url) === 1 ? $url : null;
    };

    $newsletterAction = $normalizeFormAction($newsletterAction);
    $newsletterMethod = strtoupper((string) $newsletterMethod);
    $newsletterMethod = in_array($newsletterMethod, ['GET', 'POST'], true) ? $newsletterMethod : 'POST';
?>

<footer <?php echo e($attributes->merge(['class' => $footerClasses])); ?>>
    
    <?php if (isset($component)) { $__componentOriginala8292fbc6719c22f60e6bbff9e345811 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8292fbc6719c22f60e6bbff9e345811 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.grid-layout','data' => ['gap' => $gap,'align' => 'start']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.grid-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['gap' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($gap),'align' => 'start']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($logo || $brandText || $brandDescription): ?>
            <nav class="col-12 col-md-6 col-lg-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($logo): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($logo instanceof \Illuminate\View\ComponentSlot): ?>
                        <?php echo e($logo); ?>

                    <?php else: ?>
                        <img src="<?php echo e($logo); ?>" alt="<?php echo e($brandText ?? 'Logo'); ?>" class="h-8 w-auto" />
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($brandText): ?>
                    <h6 class="footer-title"><?php echo e($brandText); ?></h6>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($brandDescription): ?>
                    <p class="text-sm opacity-70"><?php echo e($brandDescription); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </nav>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $columns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <nav class="col-12 col-md-6 col-lg-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($column['title'])): ?>
                    <h6 class="footer-title"><?php echo e($column['title']); ?></h6>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($column['links']) && is_array($column['links'])): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $column['links']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <a 
                            href="<?php echo e($normalizeHref($link['href'] ?? '#')); ?>"
                            class="link link-hover"
                            <?php if(isset($link['external']) && $link['external']): ?>
                                target="_blank" rel="noopener noreferrer"
                            <?php endif; ?>
                        >
                            <?php echo e($link['label'] ?? ''); ?>

                        </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </nav>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($newsletter): ?>
            <nav class="col-12 col-md-6 col-lg-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($newsletterTitle): ?>
                    <h6 class="footer-title"><?php echo e($newsletterTitle); ?></h6>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($newsletterDescription): ?>
                    <p class="text-sm opacity-70 mb-2"><?php echo e($newsletterDescription); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($newsletterAction): ?>
                    <form action="<?php echo e($newsletterAction); ?>" method="<?php echo e($newsletterHtmlMethod); ?>" class="flex gap-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($newsletterHtmlMethod !== 'GET'): ?>
                            <?php echo csrf_field(); ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! in_array($newsletterFormMethod, ['GET', 'POST'], true)): ?>
                            <?php echo method_field($newsletterFormMethod); ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <input 
                            type="email" 
                            name="email" 
                            placeholder="<?php echo e(__('daisy::common.email')); ?>" 
                            class="input input-sm flex-1" 
                            required 
                        />
                        <button type="submit" class="btn btn-sm btn-primary">
                            <?php echo e(__('daisy::common.subscribe')); ?>

                        </button>
                    </form>
                <?php elseif(isset($newsletter) && $newsletter instanceof \Illuminate\View\ComponentSlot): ?>
                    <?php echo e($newsletter); ?>

                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </nav>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($columns) && $columns instanceof \Illuminate\View\ComponentSlot): ?>
            <div class="col-12">
                <?php echo e($columns); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8292fbc6719c22f60e6bbff9e345811)): ?>
<?php $attributes = $__attributesOriginala8292fbc6719c22f60e6bbff9e345811; ?>
<?php unset($__attributesOriginala8292fbc6719c22f60e6bbff9e345811); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8292fbc6719c22f60e6bbff9e345811)): ?>
<?php $component = $__componentOriginala8292fbc6719c22f60e6bbff9e345811; ?>
<?php unset($__componentOriginala8292fbc6719c22f60e6bbff9e345811); ?>
<?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showDivider): ?>
        <?php if (isset($component)) { $__componentOriginal59ceec366024b397ad19f50e70d434d1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal59ceec366024b397ad19f50e70d434d1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.layout.divider','data' => ['color' => $dividerColor,'class' => 'my-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.layout.divider'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dividerColor),'class' => 'my-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal59ceec366024b397ad19f50e70d434d1)): ?>
<?php $attributes = $__attributesOriginal59ceec366024b397ad19f50e70d434d1; ?>
<?php unset($__attributesOriginal59ceec366024b397ad19f50e70d434d1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal59ceec366024b397ad19f50e70d434d1)): ?>
<?php $component = $__componentOriginal59ceec366024b397ad19f50e70d434d1; ?>
<?php unset($__componentOriginal59ceec366024b397ad19f50e70d434d1); ?>
<?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="footer-bottom flex flex-col sm:flex-row gap-4 items-center justify-between">
        
        <aside class="text-sm opacity-70">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($copyright) && $copyright instanceof \Illuminate\View\ComponentSlot): ?>
                <?php echo e($copyright); ?>

            <?php elseif($copyright): ?>
                <?php echo $copyright; ?>

            <?php elseif($copyrightText): ?>
                <p>© <?php echo e($year); ?> <?php echo e($copyrightText); ?></p>
            <?php else: ?>
                <p>© <?php echo e($year); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </aside>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($socialLinks)): ?>
            <nav class="flex gap-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $socialLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $social): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <a 
                        href="<?php echo e($normalizeHref($social['href'] ?? '#')); ?>"
                        class="btn btn-circle btn-sm btn-ghost"
                        <?php if(isset($social['external']) && $social['external']): ?>
                            target="_blank" rel="noopener noreferrer"
                        <?php endif; ?>
                        aria-label="<?php echo e($social['label'] ?? 'Social link'); ?>"
                    >
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($social['icon'])): ?>
                            <?php if (isset($component)) { $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::components.ui.advanced.icon','data' => ['name' => $social['icon']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::ui.advanced.icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($social['icon'])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $attributes = $__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__attributesOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a)): ?>
<?php $component = $__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a; ?>
<?php unset($__componentOriginal6d85bc7f45856f29b85e842d5d1ded8a); ?>
<?php endif; ?>
                        <?php else: ?>
                            <?php echo e($social['label'] ?? ''); ?>

                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </nav>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($footerBottom) && $footerBottom instanceof \Illuminate\View\ComponentSlot): ?>
            <?php echo e($footerBottom); ?>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</footer>
<?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/resources/views/components/ui/layout/footer-layout.blade.php ENDPATH**/ ?>