<!-- Chat -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Chat</h2>
    <div class="space-y-3">
        <!-- Simple -->
        <x-daisy::ui.chat-bubble align="start" name="Alice" time="12:45">
            Salut !
            <x-slot:avatar><img src="https://i.pravatar.cc/100?img=6" /></x-slot:avatar>
        </x-daisy::ui.chat-bubble>
        <x-daisy::ui.chat-bubble align="end" name="Bob" time="12:46">
            Hello !
            <x-slot:avatar><img src="https://i.pravatar.cc/100?img=7" /></x-slot:avatar>
        </x-daisy::ui.chat-bubble>

        <!-- Couleurs -->
        <div class="space-y-2">
            <x-daisy::ui.chat-bubble align="start" color="primary">What kind of nonsense is this</x-daisy::ui.chat-bubble>
            <x-daisy::ui.chat-bubble align="start" color="secondary">Put me on the Council and not make me a Master!??</x-daisy::ui.chat-bubble>
            <x-daisy::ui.chat-bubble align="start" color="accent">That's never been done in the history of the Jedi.</x-daisy::ui.chat-bubble>
            <x-daisy::ui.chat-bubble align="start" color="neutral">It's insulting!</x-daisy::ui.chat-bubble>
            <x-daisy::ui.chat-bubble align="end" color="info">Calm down, Anakin.</x-daisy::ui.chat-bubble>
            <x-daisy::ui.chat-bubble align="end" color="success">You have been given a great honor.</x-daisy::ui.chat-bubble>
            <x-daisy::ui.chat-bubble align="end" color="warning">To be on the Council at your age.</x-daisy::ui.chat-bubble>
            <x-daisy::ui.chat-bubble align="end" color="error">It's never happened before.</x-daisy::ui.chat-bubble>
        </div>

        <!-- Slots header/footer -->
        <x-daisy::ui.chat-bubble align="start">
            Using header and footer slots
            <x-slot:header>
                Yoda <time class="text-xs opacity-50">13:00</time>
            </x-slot:header>
            <x-slot:footer>
                Delivered
            </x-slot:footer>
        </x-daisy::ui.chat-bubble>
    </div>
</section>


