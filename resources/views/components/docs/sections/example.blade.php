@props([
    'name',
    'height' => '200px',
])

<section id="base" class="mt-10">
    <h2>Exemple de base</h2>
    <div class="tabs tabs-box">
        <input type="radio" name="base-example-{{ $name }}" class="tab" aria-label="Preview" checked />
        <div class="tab-content bg-base-100 p-6">
            <div class="not-prose">
                {{ $preview ?? '' }}
            </div>
        </div>
        <input type="radio" name="base-example-{{ $name }}" class="tab" aria-label="Code" />
        <div class="tab-content bg-base-100 p-6">
            {{ $code ?? '' }}
        </div>
    </div>
</section>

