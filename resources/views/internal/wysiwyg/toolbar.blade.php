<div class="daisy-kit-wysiwyg__toolbar-row" role="toolbar" aria-label="{{ __('daisy-kit::wysiwyg.toolbar') }}">
    <div class="join" data-trix-button-group="text-tools">
        @foreach ([
            ['bold', 'bold', 'b'],
            ['italic', 'italic', 'i'],
            ['strike', 'strike', null],
        ] as [$attribute, $label, $key])
            <button class="btn btn-sm btn-square btn-ghost join-item" type="button" data-trix-attribute="{{ $attribute }}" @if($key) data-trix-key="{{ $key }}" @endif aria-label="{{ __('daisy-kit::wysiwyg.'.$label) }}" title="{{ __('daisy-kit::wysiwyg.'.$label) }}">
                @include('daisy-kit::internal.wysiwyg.icon', ['name' => $label])
            </button>
        @endforeach
        <button class="btn btn-sm btn-square btn-ghost join-item" type="button" data-trix-attribute="href" data-trix-action="link" data-trix-key="k" aria-label="{{ __('daisy-kit::wysiwyg.link') }}" title="{{ __('daisy-kit::wysiwyg.link') }}">
            @include('daisy-kit::internal.wysiwyg.icon', ['name' => 'link'])
        </button>
    </div>
    <div class="join" data-trix-button-group="block-tools">
        @foreach ([
            ['heading1', 'heading'],
            ['quote', 'quote'],
            ['code', 'code'],
            ['bullet', 'bullets'],
            ['number', 'numbers'],
        ] as [$attribute, $label])
            <button class="btn btn-sm btn-square btn-ghost join-item" type="button" data-trix-attribute="{{ $attribute }}" aria-label="{{ __('daisy-kit::wysiwyg.'.$label) }}" title="{{ __('daisy-kit::wysiwyg.'.$label) }}">
                @include('daisy-kit::internal.wysiwyg.icon', ['name' => $label])
            </button>
        @endforeach
        <button class="btn btn-sm btn-square btn-ghost join-item" type="button" data-trix-action="decreaseNestingLevel" aria-label="{{ __('daisy-kit::wysiwyg.outdent') }}" title="{{ __('daisy-kit::wysiwyg.outdent') }}">
            @include('daisy-kit::internal.wysiwyg.icon', ['name' => 'outdent'])
        </button>
        <button class="btn btn-sm btn-square btn-ghost join-item" type="button" data-trix-action="increaseNestingLevel" aria-label="{{ __('daisy-kit::wysiwyg.indent') }}" title="{{ __('daisy-kit::wysiwyg.indent') }}">
            @include('daisy-kit::internal.wysiwyg.icon', ['name' => 'indent'])
        </button>
    </div>
    @if ($attachments)
        <div class="join" data-trix-button-group="file-tools">
            <button class="btn btn-sm btn-square btn-ghost join-item" type="button" data-trix-action="attachFiles" aria-label="{{ __('daisy-kit::wysiwyg.attach') }}" title="{{ __('daisy-kit::wysiwyg.attach') }}">
                @include('daisy-kit::internal.wysiwyg.icon', ['name' => 'attach'])
            </button>
        </div>
    @endif
    <div class="join" data-trix-button-group="history-tools">
        <button class="btn btn-sm btn-square btn-ghost join-item" type="button" data-trix-action="undo" data-trix-key="z" aria-label="{{ __('daisy-kit::wysiwyg.undo') }}" title="{{ __('daisy-kit::wysiwyg.undo') }}">
            @include('daisy-kit::internal.wysiwyg.icon', ['name' => 'undo'])
        </button>
        <button class="btn btn-sm btn-square btn-ghost join-item" type="button" data-trix-action="redo" data-trix-key="shift+z" aria-label="{{ __('daisy-kit::wysiwyg.redo') }}" title="{{ __('daisy-kit::wysiwyg.redo') }}">
            @include('daisy-kit::internal.wysiwyg.icon', ['name' => 'redo'])
        </button>
    </div>
</div>
<div class="daisy-kit-wysiwyg__dialogs" data-trix-dialogs>
    <div class="daisy-kit-wysiwyg__dialog" data-trix-dialog="href" data-trix-dialog-attribute="href">
        <label class="input input-sm w-full">
            <span class="sr-only">{{ __('daisy-kit::wysiwyg.url') }}</span>
            <input type="url" name="href" placeholder="{{ __('daisy-kit::wysiwyg.url_placeholder') }}" data-trix-validate-href required data-trix-input>
        </label>
        <div class="join">
            <input class="btn btn-sm join-item" type="button" value="{{ __('daisy-kit::wysiwyg.apply_link') }}" data-trix-method="setAttribute">
            <input class="btn btn-sm join-item" type="button" value="{{ __('daisy-kit::wysiwyg.remove_link') }}" data-trix-method="removeAttribute">
        </div>
    </div>
</div>
