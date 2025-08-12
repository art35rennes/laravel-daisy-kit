<!-- Table -->
<section class="space-y-4 bg-base-200 p-6 rounded-box">
    <h2 class="text-lg font-medium">Table</h2>
    <div class="space-y-4">
        <!-- Basique -->
        <x-daisy::ui.table :headers="['#','Name','Job','Favorite Color']"
            :rows="[
                ['1','Cy Ganderton','Quality Control Specialist','Blue'],
                ['2','Hart Hagerty','Desktop Support Technician','Purple'],
                ['3','Brice Swyre','Tax Accountant','Red'],
            ]"
            :rowHeaders="true" selection="multiple" :showRowNumbers="true" :offset="1"
        />

        <!-- Avec bordure et fond -->
        <x-daisy::ui.table containerClass="rounded-box border border-base-content/5 bg-base-100"
            :headers="['#','Name','Job','Favorite Color']"
            :rows="[
                ['1','Cy Ganderton','Quality Control Specialist','Blue'],
                ['2','Hart Hagerty','Desktop Support Technician','Purple'],
                ['3','Brice Swyre','Tax Accountant','Red'],
            ]"
            :rowHeaders="true" selection="single" :showRowNumbers="true" :offset="11"
        />

        <!-- Zebra + tailles + pin -->
        <x-daisy::ui.table zebra size="sm" :pinRows="true" :pinCols="true" overflowX overflowY
            :headers="['#','Name','Job','Company','Location','Last Login','Favorite Color','']"
            :rows="[
                ['1','Cy Ganderton','Quality Control Specialist','Littel, Schaden and Vandervort','Canada','12/16/2020','Blue','1'],
                ['2','Hart Hagerty','Desktop Support Technician','Zemlak, Daniel and Leannon','United States','12/5/2020','Purple','2'],
                ['3','Brice Swyre','Tax Accountant','Carroll Group','China','8/15/2020','Red','3'],
            ]"
            :footer="['','Name','Job','Company','Location','Last Login','Favorite Color','']"
            :rowHeaders="true" selection="multiple" :showRowNumbers="true" :offset="21"
        />
    </div>
</section>


