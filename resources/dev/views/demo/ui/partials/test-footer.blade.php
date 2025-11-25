<!-- Footer -->
<section class="space-y-6 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Footer</h2>
    
    <!-- Footer basique -->
    <x-daisy::ui.layout.footer horizontal class="rounded-box">
        <nav>
            <h6 class="footer-title">Services</h6>
            <a class="link link-hover">Branding</a>
            <a class="link link-hover">Design</a>
            <a class="link link-hover">Marketing</a>
        </nav>
        <nav>
            <h6 class="footer-title">Company</h6>
            <a class="link link-hover">About</a>
            <a class="link link-hover">Contact</a>
        </nav>
        <nav>
            <h6 class="footer-title">Legal</h6>
            <a class="link link-hover">Terms</a>
            <a class="link link-hover">Privacy</a>
        </nav>
    </x-daisy::ui.layout.footer>
</section>

<!-- Footer Layout avec colonnes -->
<section class="space-y-6 bg-base-200 p-6 rounded-box mt-8">
    <h2 class="text-lg font-medium">Footer Layout</h2>
    <p class="text-sm text-base-content/70">Footer avancé avec gestion de colonnes, copyright, réseaux sociaux et newsletter.</p>
    
    <x-daisy::ui.layout.footer-layout
        :columns="[
            [
                'title' => 'Services',
                'links' => [
                    ['label' => 'Branding', 'href' => '#'],
                    ['label' => 'Design', 'href' => '#'],
                    ['label' => 'Marketing', 'href' => '#'],
                ]
            ],
            [
                'title' => 'Company',
                'links' => [
                    ['label' => 'About', 'href' => '#'],
                    ['label' => 'Contact', 'href' => '#'],
                    ['label' => 'Careers', 'href' => '#'],
                ]
            ],
            [
                'title' => 'Legal',
                'links' => [
                    ['label' => 'Terms', 'href' => '#'],
                    ['label' => 'Privacy', 'href' => '#'],
                    ['label' => 'Cookies', 'href' => '#'],
                ]
            ],
        ]"
        brand-text="Mon Entreprise"
        brand-description="Créons ensemble des solutions innovantes."
        :social-links="[
            ['icon' => 'facebook', 'href' => '#', 'label' => 'Facebook', 'external' => true],
            ['icon' => 'twitter', 'href' => '#', 'label' => 'Twitter', 'external' => true],
            ['icon' => 'instagram', 'href' => '#', 'label' => 'Instagram', 'external' => true],
            ['icon' => 'linkedin', 'href' => '#', 'label' => 'LinkedIn', 'external' => true],
        ]"
        copyright-text="Mon Entreprise. Tous droits réservés."
        :newsletter="true"
        newsletter-title="Newsletter"
        newsletter-description="Restez informé de nos dernières actualités."
        newsletter-action="#"
        class="rounded-box"
    />
</section>

<!-- Footer Layout minimal -->
<section class="space-y-6 bg-base-200 p-6 rounded-box mt-8">
    <h2 class="text-lg font-medium">Footer Layout Minimal</h2>
    
    <x-daisy::ui.layout.footer-layout
        :columns="[
            [
                'title' => 'Navigation',
                'links' => [
                    ['label' => 'Accueil', 'href' => '#'],
                    ['label' => 'À propos', 'href' => '#'],
                    ['label' => 'Contact', 'href' => '#'],
                ]
            ],
        ]"
        copyright-text="Mon Entreprise"
        :show-divider="false"
        class="rounded-box"
    />
</section>


