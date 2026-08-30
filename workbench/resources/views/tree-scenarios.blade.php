<section class="min-w-0 space-y-6" aria-labelledby="tree-heading">
    <h2 id="tree-heading" class="text-2xl font-semibold">Tree</h2>
    <div class="grid min-w-0 items-start gap-6 lg:grid-cols-2">
        <article class="min-w-0 space-y-3" id="tree-classification">
            <h3 class="text-lg font-medium">Document classification</h3>
            <p class="text-sm text-base-content/70">Choose an area for this document. Search keeps the parent path visible.</p>
            <x-daisy-kit::tree id="classification-tree" label="Document classification"
                :items="\Workbench\App\TreeExamples::classification()" value="getting-started"
                name="document_area" :searchable="true" search-match="fuzzy" />
        </article>
        <article class="min-w-0 space-y-3" id="tree-permissions">
            <h3 class="text-lg font-medium">Team permissions</h3>
            <p class="text-sm text-base-content/70">Select permissions across branches. Restricted actions remain visible but cannot be selected.</p>
            <x-daisy-kit::tree id="permissions-tree" label="Team permissions"
                :items="\Workbench\App\TreeExamples::permissions()" :multiple="true"
                :value="['projects-records-read', 'people-records-read']"
                name="permissions" :searchable="true" search-mode="manual" />
        </article>
        <article class="min-w-0 space-y-3" id="tree-catalogue">
            <h3 class="text-lg font-medium">Regional catalogue</h3>
            <p class="text-sm text-base-content/70">Choose entire regions or individual services. Branches load independently; search reaches the remote catalogue.</p>
            <x-daisy-kit::tree id="catalogue-tree" label="Regional catalogue"
                :items="\Workbench\App\TreeExamples::catalogue()" :multiple="true" value-mode="selected-roots"
                name="regions" :searchable="true" search-mode="manual"
                :search-source="route('workbench.tree.catalogueSearch')" persistence-key="workbench-catalogue" />
        </article>
        <article class="min-w-0 space-y-3" id="tree-teams">
            <h3 class="text-lg font-medium">Project reviewers</h3>
            <p class="text-sm text-base-content/70">Custom Blade presentation adds roles and badges while the package owns selection and keyboard controls.</p>
            <x-daisy-kit::tree id="reviewers-tree" label="Project reviewers"
                :items="\Workbench\App\TreeExamples::teams()" :multiple="true" :value="['platform-0']"
                :initial-expand-paths="[['platform', 'platform-0']]"
                name="reviewers" :searchable="true" node-view="workbench::tree-node" />
        </article>
    </div>
</section>
