<div
    @class([
        'p-4',
        'flex flex-wrap' => $row
    ])
    @attributes($attributes)
>
    <div class="w-full flex flex-wrap">
        @if (($type === 'create' || $type === 'edit') && $nav_redirect)
            @include(admin_template('components.form-footer.actions-after-save'), [
                'select' => session('_after', 'index'),
                'type' => $type,
                'lang_to_the_list' => __('admin.to_the_list'),
                'lang_add_more' => __('admin.add_more'),
                'lang_edit_further' => __('admin.edit_further'),
            ])
        @endif

        @foreach($contents as $content)
            {!! $content !!}
        @endforeach

        @if(isset($group) && $group)
            <div class="ml-auto">
                {!! $group !!}
            </div>
        @endif
    </div>
</div>

{{--<div--}}
{{--    @class(['card-footer', 'row' => $row])--}}
{{--    @attributes($attributes)--}}
{{-->--}}
{{--    <div class="row">--}}

{{--        @if (($type === 'create' || $type === 'edit') && $nav_redirect)--}}

{{--            @include(admin_template('components.form-footer.actions-after-save'), [--}}
{{--                'select' => session('_after', 'index'),--}}
{{--                'type' => $type,--}}
{{--                'lang_to_the_list' => __('admin.to_the_list'),--}}
{{--                'lang_add_more' => __('admin.add_more'),--}}
{{--                'lang_edit_further' => __('admin.edit_further'),--}}
{{--            ])--}}
{{--        @endif--}}

{{--        @foreach($contents as $content)--}}
{{--            {!! $content !!}--}}
{{--        @endforeach--}}

{{--        @if(isset($group) && $group)--}}
{{--            <div class="col text-right">--}}
{{--                {!! $group !!}--}}
{{--            </div>--}}
{{--        @endif--}}
{{--    </div>--}}
{{--</div>--}}
