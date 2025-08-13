<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Charts (Chart.js + DaisyUI)</h2>
    <div class="grid md:grid-cols-2 gap-6">
        <div class="space-y-2">
            <div class="label"><span class="label-text">Bar · palette par défaut DaisyUI</span></div>
            <x-daisy::ui.chart
                :labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']"
                :datasets="[
                    ['label' => 'Ventes', 'data' => [12, 19, 3, 5, 2, 3]],
                    ['label' => 'Retours', 'data' => [2, 3, 20, 5, 1, 4], 'color' => 'secondary'],
                ]"
                height="280px"
            />
        </div>

        <div class="space-y-2">
            <div class="label"><span class="label-text">Line · couleurs override</span></div>
            <x-daisy::ui.chart
                type="line"
                :labels="['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']"
                :datasets="[
                    ['label' => 'Sessions', 'data' => [120, 90, 150, 130, 170, 180, 160], 'fillAlpha' => 0.6],
                ]"
                :colors="['#22c55e']"
                :options="['elements' => ['line' => ['tension' => 0.35]]]"
                height="280px"
            />
        </div>

        <div class="space-y-2">
            <div class="label"><span class="label-text">Doughnut · palette personnalisée</span></div>
            <x-daisy::ui.chart
                type="doughnut"
                :labels="['Chrome', 'Safari', 'Firefox', 'Edge']"
                :datasets="[
                    ['label' => 'Parts', 'data' => [63, 19, 11, 7]]
                ]"
                :palette="['primary','accent','warning','error']"
                height="240px"
            />
        </div>

        <div class="space-y-2">
            <div class="label"><span class="label-text">Radar · responsive</span></div>
            <x-daisy::ui.chart
                type="radar"
                :labels="['Perf', 'UX', 'SEO', 'Access', 'Security']"
                :datasets="[
                    ['label' => 'Score', 'data' => [80, 74, 68, 90, 77], 'fillAlpha' => 0.6]
                ]"
                :options="['elements' => ['line' => ['borderWidth' => 2]]]"
                height="280px"
            />
        </div>

        <div class="space-y-2">
            <div class="label"><span class="label-text">Area · alias de Line avec fill</span></div>
            <x-daisy::ui.chart
                type="area"
                :labels="['Q1','Q2','Q3','Q4']"
                :datasets="[
                    ['label' => 'CA', 'data' => [120, 150, 110, 170], 'fillAlpha' => 0.6]
                ]"
                :options="['elements' => ['line' => ['tension' => 0.3]], 'plugins' => ['legend' => ['display' => true]]]"
                height="240px"
            />
        </div>

        <div class="space-y-2">
            <div class="label"><span class="label-text">Pie · basique</span></div>
            <x-daisy::ui.chart
                type="pie"
                :labels="['A', 'B', 'C']"
                :datasets="[
                    ['label' => 'Répartition', 'data' => [30, 45, 25]]
                ]"
                height="240px"
            />
        </div>

        <div class="space-y-2">
            <div class="label"><span class="label-text">Polar Area · basique</span></div>
            <x-daisy::ui.chart
                type="polarArea"
                :labels="['North','South','East','West']"
                :datasets="[
                    ['label' => 'Power', 'data' => [11, 16, 7, 14]]
                ]"
                height="240px"
            />
        </div>

        <div class="space-y-2">
            <div class="label"><span class="label-text">Mixed · bar + line</span></div>
            <x-daisy::ui.chart
                :labels="['Jan','Feb','Mar','Apr','May','Jun']"
                :datasets="[
                    ['type' => 'bar', 'label' => 'Ventes', 'data' => [12, 19, 3, 5, 2, 3]],
                    ['type' => 'line', 'label' => 'Tendance', 'data' => [10, 12, 8, 9, 11, 13], 'fillAlpha' => 0.6]
                ]"
                height="280px"
            />
        </div>

        <div class="space-y-2">
            <div class="label"><span class="label-text">Scatter · dot chart</span></div>
            <x-daisy::ui.chart
                type="scatter"
                :labels="[]"
                :datasets="[
                    ['label' => 'Samples', 'data' => [
                        ['x' => -10, 'y' => 0], ['x' => -7, 'y' => 8], ['x' => -3, 'y' => -3],
                        ['x' => 0, 'y' => 10], ['x' => 2, 'y' => -5], ['x' => 5, 'y' => 4],
                        ['x' => 9, 'y' => 12], ['x' => 10, 'y' => 5]
                    ]]
                ]"
                :options="[
                    'elements' => ['point' => ['radius' => 4]],
                    'scales' => [
                        'x' => ['title' => ['display' => true, 'text' => 'X']],
                        'y' => ['title' => ['display' => true, 'text' => 'Y']]
                    ]
                ]"
                height="280px"
            />
        </div>

        <div class="space-y-2">
            <div class="label"><span class="label-text">Bubble · tailles variables</span></div>
            <x-daisy::ui.chart
                type="bubble"
                :labels="[]"
                :datasets="[
                    ['label' => 'Bubbles', 'data' => [
                        ['x' => 4, 'y' => 5, 'r' => 6], ['x' => 8, 'y' => 14, 'r' => 8],
                        ['x' => 12, 'y' => 7, 'r' => 10], ['x' => 6, 'y' => 3, 'r' => 5],
                        ['x' => 15, 'y' => 10, 'r' => 7]
                    ], 'borderWidth' => 1, 'fillAlpha' => 0.6]
                ]"
                :options="[
                    'scales' => [
                        'x' => ['title' => ['display' => true, 'text' => 'X']],
                        'y' => ['title' => ['display' => true, 'text' => 'Y']]
                    ]
                ]"
                height="280px"
            />
        </div>
    </div>
</section>


