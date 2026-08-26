    <?php if (isset($component)) { $__componentOriginal7c0bb14f06d3c30fb800b111105387ea = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7c0bb14f06d3c30fb800b111105387ea = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::.templates.form.form-simple','data' => ['id' => 'simple-business-form','autocomplete' => 'off']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::templates.form.form-simple'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'simple-business-form','autocomplete' => 'off']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7c0bb14f06d3c30fb800b111105387ea)): ?>
<?php $attributes = $__attributesOriginal7c0bb14f06d3c30fb800b111105387ea; ?>
<?php unset($__attributesOriginal7c0bb14f06d3c30fb800b111105387ea); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7c0bb14f06d3c30fb800b111105387ea)): ?>
<?php $component = $__componentOriginal7c0bb14f06d3c30fb800b111105387ea; ?>
<?php unset($__componentOriginal7c0bb14f06d3c30fb800b111105387ea); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal34c31629ac42697e0fb9850501ab4565 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal34c31629ac42697e0fb9850501ab4565 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::.templates.form.form-inline','data' => ['id' => 'inline-business-form','autocomplete' => 'off']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::templates.form.form-inline'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'inline-business-form','autocomplete' => 'off']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal34c31629ac42697e0fb9850501ab4565)): ?>
<?php $attributes = $__attributesOriginal34c31629ac42697e0fb9850501ab4565; ?>
<?php unset($__attributesOriginal34c31629ac42697e0fb9850501ab4565); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal34c31629ac42697e0fb9850501ab4565)): ?>
<?php $component = $__componentOriginal34c31629ac42697e0fb9850501ab4565; ?>
<?php unset($__componentOriginal34c31629ac42697e0fb9850501ab4565); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginalafab2932311e34f315e982104ef73d81 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalafab2932311e34f315e982104ef73d81 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::.templates.form.form-with-tabs','data' => ['id' => 'tabs-business-form','autocomplete' => 'off','tabs' => [['id' => 'general', 'label' => 'General']]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::templates.form.form-with-tabs'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'tabs-business-form','autocomplete' => 'off','tabs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([['id' => 'general', 'label' => 'General']])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalafab2932311e34f315e982104ef73d81)): ?>
<?php $attributes = $__attributesOriginalafab2932311e34f315e982104ef73d81; ?>
<?php unset($__attributesOriginalafab2932311e34f315e982104ef73d81); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalafab2932311e34f315e982104ef73d81)): ?>
<?php $component = $__componentOriginalafab2932311e34f315e982104ef73d81; ?>
<?php unset($__componentOriginalafab2932311e34f315e982104ef73d81); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginal4e67a34a3bae45620c623aaaf0ddc1b9 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4e67a34a3bae45620c623aaaf0ddc1b9 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'daisy::.templates.form.form-wizard','data' => ['id' => 'wizard-business-form','autocomplete' => 'off','steps' => [['key' => 'details', 'label' => 'Details']]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('daisy::templates.form.form-wizard'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'wizard-business-form','autocomplete' => 'off','steps' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([['key' => 'details', 'label' => 'Details']])]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4e67a34a3bae45620c623aaaf0ddc1b9)): ?>
<?php $attributes = $__attributesOriginal4e67a34a3bae45620c623aaaf0ddc1b9; ?>
<?php unset($__attributesOriginal4e67a34a3bae45620c623aaaf0ddc1b9); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4e67a34a3bae45620c623aaaf0ddc1b9)): ?>
<?php $component = $__componentOriginal4e67a34a3bae45620c623aaaf0ddc1b9; ?>
<?php unset($__componentOriginal4e67a34a3bae45620c623aaaf0ddc1b9); ?>
<?php endif; ?><?php /**PATH /Users/asicard/.codex/worktrees/2849/laravel-daisy-kit/tests/Fixtures/laravel/storage/framework/views/aa5e26381044cda2f42e97b1edd95065.blade.php ENDPATH**/ ?>