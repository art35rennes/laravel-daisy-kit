<!-- List -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">List</h2>
    <div class="grid md:grid-cols-2 gap-6">
        <!-- Exemple simple -->
        <x-daisy::ui.list :bg="true" :rounded="true" shadow="md" title="Most played songs this week">
            <x-daisy::ui.list-row>
                <div><img class="w-10 h-10 rounded-box" src="https://i.pravatar.cc/80?img=48" alt="Avatar"/></div>
                <div>
                    <div>Dio Lupa</div>
                    <div class="text-xs uppercase font-semibold opacity-60">Remaining Reason</div>
                </div>
                <button class="btn btn-square btn-ghost">▶</button>
                <button class="btn btn-square btn-ghost">❤</button>
            </x-daisy::ui.list-row>
            <x-daisy::ui.list-row>
                <div><img class="w-10 h-10 rounded-box" src="https://i.pravatar.cc/80?img=51" alt="Avatar"/></div>
                <div>
                    <div>Ellie Beilish</div>
                    <div class="text-xs uppercase font-semibold opacity-60">Bears of a fever</div>
                </div>
                <button class="btn btn-square btn-ghost">▶</button>
                <button class="btn btn-square btn-ghost">❤</button>
            </x-daisy::ui.list-row>
            <x-daisy::ui.list-row>
                <div><img class="w-10 h-10 rounded-box" src="https://i.pravatar.cc/80?img=49" alt="Avatar"/></div>
                <div>
                    <div>Sabrino Gardener</div>
                    <div class="text-xs uppercase font-semibold opacity-60">Cappuccino</div>
                </div>
                <button class="btn btn-square btn-ghost">▶</button>
                <button class="btn btn-square btn-ghost">❤</button>
            </x-daisy::ui.list-row>
        </x-daisy::ui.list>

        <!-- Exemple avec list-col-wrap (colonne qui passe à la ligne) -->
        <x-daisy::ui.list :bg="true" :rounded="true" shadow="md" title="With descriptions">
            <x-daisy::ui.list-row>
                <div><img class="w-10 h-10 rounded-box" src="https://i.pravatar.cc/80?img=48" alt="Avatar"/></div>
                <div>
                    <div>Dio Lupa</div>
                    <div class="text-xs uppercase font-semibold opacity-60">Remaining Reason</div>
                </div>
                <p class="list-col-wrap text-xs opacity-80">
                    "Remaining Reason" blends introspective lyrics with a dynamic beat, becoming one of Dio Lupa's most iconic tracks.
                </p>
                <button class="btn btn-square btn-ghost">▶</button>
                <button class="btn btn-square btn-ghost">❤</button>
            </x-daisy::ui.list-row>
            <x-daisy::ui.list-row>
                <div><img class="w-10 h-10 rounded-box" src="https://i.pravatar.cc/80?img=51" alt="Avatar"/></div>
                <div>
                    <div>Ellie Beilish</div>
                    <div class="text-xs uppercase font-semibold opacity-60">Bears of a fever</div>
                </div>
                <p class="list-col-wrap text-xs opacity-80">
                    "Bears of a Fever" captivated audiences with its intense energy and mysterious lyrics.
                </p>
                <button class="btn btn-square btn-ghost">▶</button>
                <button class="btn btn-square btn-ghost">❤</button>
            </x-daisy::ui.list-row>
        </x-daisy::ui.list>
    </div>
</section>


