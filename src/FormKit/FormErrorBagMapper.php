<?php

namespace Art35rennes\DaisyKit\FormKit;

use Illuminate\Contracts\Support\MessageBag;

/**
 * Normalizes Laravel validation errors into the nested string arrays expected by the form viewer Blade partials and JSON payloads.
 */
class FormErrorBagMapper
{
    /**
     * Converts a {@see MessageBag} or associative array into `fieldKey => list<string>` messages.
     *
     * @param  MessageBag|array<string, mixed>  $errors  Laravel bag or raw array shaped like `$errors->toArray()`.
     * @return array<string, array<int, string>>
     */
    public function map(MessageBag|array $errors): array
    {
        $messages = $errors instanceof MessageBag ? $errors->toArray() : $errors;

        return collect($messages)
            ->map(fn (mixed $message) => array_values(array_map('strval', (array) $message)))
            ->all();
    }
}
