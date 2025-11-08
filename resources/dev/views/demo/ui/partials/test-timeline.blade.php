<!-- Timeline -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Timeline</h2>
    <div class="space-y-6">
        <!-- Texte des deux côtés + icône -->
        <x-daisy::ui.data-display.timeline :items="[
            ['when' => '1984', 'title' => 'First Macintosh computer'],
            ['when' => '1998', 'title' => 'iMac'],
            ['when' => '2001', 'title' => 'iPod'],
            ['when' => '2007', 'title' => 'iPhone'],
            ['when' => '2015', 'title' => 'Apple Watch', 'hrAfter' => false],
        ]" />

        <!-- Avec contenu riche (startHtml / endHtml) -->
        <x-daisy::ui.data-display.timeline :items="[
            [
                'startHtml' => '<time class=\'font-mono italic\'>1984</time>',
                'endHtml' => '<div class=\'timeline-box\'>First Macintosh computer</div>',
                'hrAfter' => true,
            ],
            [
                'startHtml' => '<time class=\'font-mono italic\'>1998</time>',
                'endHtml' => '<div class=\'md:mb-10\'><div class=\'text-lg font-black\'>iMac</div>iMac is a family of all-in-one Mac desktop computers…</div>',
                'hrAfter' => true,
            ],
            [
                'startHtml' => '<time class=\'font-mono italic\'>2001</time>',
                'endHtml' => '<div class=\'mb-10 md:text-end\'><div class=\'text-lg font-black\'>iPod</div>The iPod is a discontinued series of portable media players…</div>',
                'hrAfter' => true,
            ],
            [
                'startHtml' => '<time class=\'font-mono italic\'>2007</time>',
                'endHtml' => '<div class=\'md:mb-10\'><div class=\'text-lg font-black\'>iPhone</div>iPhone is a line of smartphones produced by Apple Inc.…</div>',
                'hrAfter' => true,
            ],
            [
                'startHtml' => '<time class=\'font-mono italic\'>2015</time>',
                'endHtml' => '<div class=\'mb-10 md:text-end\'><div class=\'text-lg font-black\'>Apple Watch</div>The Apple Watch is a line of smartwatches…</div>',
                'hrAfter' => false,
            ],
        ]" />
    </div>
</section>


