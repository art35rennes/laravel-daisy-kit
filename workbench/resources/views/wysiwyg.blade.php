<section class="space-y-6" aria-label="Rich text examples">
    @if(session()->has('workbench.review.saved'))
        <div class="alert alert-success" role="status">Article saved.</div>
    @endif

    <form action="{{ route('workbench.reviews.store') }}" method="post">
        @csrf
        <input name="return_to" type="hidden" value="wysiwyg">
        <x-daisy-kit::wysiwyg
            name="article_body"
            label="Article body"
            placeholder="Write the article"
            :attachments="true"
            :required="true"
            value="<h1>Release notes</h1><p>Describe the customer outcome.</p>"
        />
        <div class="mt-4 flex flex-wrap gap-2">
            <button class="btn" type="reset">Reset article</button>
            <button class="btn btn-primary" type="submit">Save article</button>
        </div>
    </form>

    <div data-theme="dark">
        <x-daisy-kit::wysiwyg
            name="read_only_article"
            label="Read-only article"
            :readonly="true"
            :show-toolbar="false"
            value="<p>This content can be read but not edited.</p>"
        />
    </div>
</section>
