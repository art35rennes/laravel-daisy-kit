<?php

use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\View\ComponentSlot;

if (! function_exists('renderComponent')) {
    /**
     * Helper pour rendre un composant Blade et normaliser le HTML.
     */
    function renderComponent(string $view, array $data = []): string
    {
        // Normaliser le slot: beaucoup de composants s'attendent à un ComponentSlot
        if (array_key_exists('slot', $data) && ! $data['slot'] instanceof ComponentSlot) {
            $data['slot'] = new ComponentSlot((string) $data['slot']);
        }

        $html = View::make($view, $data)->render();

        // Normalisation basique : supprimer les espaces multiples, normaliser les retours à la ligne
        $html = preg_replace('/\s+/', ' ', $html);
        $html = preg_replace('/>\s+</', '><', $html);
        $html = trim($html);

        return $html;
    }
}

