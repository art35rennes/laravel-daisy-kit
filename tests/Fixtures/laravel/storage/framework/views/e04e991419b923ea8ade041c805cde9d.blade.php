    <x-daisy::layout.sidebar-layout
        title="Dashboard"
        variant="slim"
        :force-collapsed="true"
        :collapsible="false"
        :sections="[['items' => [['label' => 'Home', 'href' => '/home', 'icon' => 'house']]]]"
    >
        Content
    </x-daisy::layout.sidebar-layout>