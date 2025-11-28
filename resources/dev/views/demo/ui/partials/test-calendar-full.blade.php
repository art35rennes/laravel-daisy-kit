<!-- Calendar Full (sans dépendance externe) -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Calendar · Full</h2>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Données statiques -->
        <div class="bg-base-100 card-border shadow rounded-box p-4">
            <div class="text-sm font-medium opacity-70 mb-2">Static events (month)</div>
            <x-daisy::ui.advanced.calendar-full
                view="month"
                :events="[
                    ['id'=>1,'title'=>'All Day Event','start'=>date('Y-m-01'),'allDay'=>true],
                    ['id'=>2,'title'=>'Meeting','start'=>date('Y-m-12').' 10:30','end'=>date('Y-m-12').' 12:30','color'=>'oklch(var(--s))'],
                    ['id'=>3,'title'=>'Lunch','start'=>date('Y-m-12').' 12:00'],
                    ['id'=>4,'title'=>'Click for Google','start'=>date('Y-m-28'),'url'=>'https://google.com'],
                    ['id'=>5,'title'=>'Birthday Party','start'=>date('Y-m-14').' 07:00']
                ]"
                class="h-full"
            />
        </div>

        <!-- Chargement AJAX -->
        <div class="bg-base-100 card-border shadow rounded-box p-4">
            <div class="text-sm font-medium opacity-70 mb-2">AJAX events (week)</div>
            <x-daisy::ui.advanced.calendar-full view="week" eventsUrl="{{ route('demo.calendar.events') }}" />
        </div>
    </div>
</section>


