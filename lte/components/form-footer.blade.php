<div
    @class(array_merge(['card-footer', 'row' => $row], $classes))
    @attributes($attributes)
>
    <div class="row">

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
            <div class="col text-right">
                {!! $group !!}
            </div>
        @endif
    </div>
</div>
