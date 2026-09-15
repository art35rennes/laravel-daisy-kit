@php
    $exampleJson = json_encode(['enabled' => true, 'retries' => 3], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
    $examplePhp = '<?php'.PHP_EOL.'echo "Welcome";';
@endphp
<section class="space-y-6" aria-label="Code examples">
    <form>
        <x-daisy-kit::code-editor name="code" label="Configuration" filename="settings.json" language="json" :required="true" :nonce="$codeEditorNonce ?? null" :value="$exampleJson" />
        <button class="btn mt-4" type="reset">Reset configuration</button>
        <button class="btn btn-primary mt-4" type="submit">Apply configuration</button>
    </form>
    <div data-theme="dark">
        <x-daisy-kit::code-editor label="Read-only example" filename="welcome.php" language="php" :read-only="true" :nonce="$codeEditorNonce ?? null" :value="$examplePhp" />
    </div>
</section>
