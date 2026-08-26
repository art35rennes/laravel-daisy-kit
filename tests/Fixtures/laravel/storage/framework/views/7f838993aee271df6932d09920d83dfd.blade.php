    <x-daisy::ui.data-display.table
        row-key="id"
        :columns="[['key' => 'actions', 'type' => 'actions']]"
        :rows="[['id' => 'user-1', 'actions' => ['action' => 'remove&amp;quot; onclick=&amp;quot;alert(1)', 'label' => '&lt;script&gt;alert(1)&lt;/script&gt;', 'variant' => 'unknown']]]"
    />