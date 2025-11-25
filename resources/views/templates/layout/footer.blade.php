@props([
    'title' => null,
    'theme' => null,
    // Content container
    'container' => 'container mx-auto p-6',
    // Footer options
    'footerBg' => 'base-200',
    'footerText' => 'base-content',
    'footerPadding' => 'p-10',
    'footerCenter' => false,
    'footerHorizontal' => false,
    'footerHorizontalAt' => null,
    'footerColumns' => [],
    'footerLogo' => null,
    'footerBrandText' => null,
    'footerBrandDescription' => null,
    'footerCopyright' => null,
    'footerCopyrightYear' => null,
    'footerCopyrightText' => null,
    'footerSocialLinks' => [],
    'footerNewsletter' => false,
    'footerNewsletterTitle' => null,
    'footerNewsletterDescription' => null,
    'footerNewsletterAction' => null,
    'footerNewsletterMethod' => 'POST',
    'footerShowDivider' => true,
    'footerDividerColor' => null,
])

<x-daisy::layout.app :title="$title" :theme="$theme" :container="false">
    {{-- Main content --}}
    <main class="{{ $container }} min-h-screen">
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <x-daisy::ui.layout.footer-layout
        :bg="$footerBg"
        :text="$footerText"
        :padding="$footerPadding"
        :center="$footerCenter"
        :horizontal="$footerHorizontal"
        :horizontalAt="$footerHorizontalAt"
        :columns="$footerColumns"
        :logo="$footerLogo"
        :brandText="$footerBrandText"
        :brandDescription="$footerBrandDescription"
        :copyright="$footerCopyright"
        :copyrightYear="$footerCopyrightYear"
        :copyrightText="$footerCopyrightText"
        :socialLinks="$footerSocialLinks"
        :newsletter="$footerNewsletter"
        :newsletterTitle="$footerNewsletterTitle"
        :newsletterDescription="$footerNewsletterDescription"
        :newsletterAction="$footerNewsletterAction"
        :newsletterMethod="$footerNewsletterMethod"
        :showDivider="$footerShowDivider"
        :dividerColor="$footerDividerColor"
    >
        @if(isset($columns) && $columns instanceof \Illuminate\View\ComponentSlot)
            <x-slot:columns>{{ $columns }}</x-slot:columns>
        @endif
        @if(isset($copyright) && $copyright instanceof \Illuminate\View\ComponentSlot)
            <x-slot:copyright>{{ $copyright }}</x-slot:copyright>
        @endif
        @if(isset($footerBottom) && $footerBottom instanceof \Illuminate\View\ComponentSlot)
            <x-slot:footerBottom>{{ $footerBottom }}</x-slot:footerBottom>
        @endif
    </x-daisy::ui.layout.footer-layout>
</x-daisy::layout.app>


