<div
    @class(['form-group row'])
    @attributes($attributes)
    data-label-width="{{ $label_width }}"
    data-vertical="@json($vertical)"
>
    @if(!$reversed)
        <label for="input_updated_at" @class(['col-sm-' . $label_width => ! $vertical])>{{ $title }}</label>
    @endif
    <div @class(['col-sm-' . (12 - $label_width) => !$vertical, 'col-sm-12 pl-0' => $vertical])>

        <ul class="nav nav-tabs" id="{{ $id }}-tab" role="tablist">
            @foreach($inside_inputs as $lang => $input)
                <li class="nav-item">
                    <a @class(['nav-link', 'active' => $lang === $current_lang]) id="{{ $id }}-{{ $lang }}-tab" data-toggle="pill" href="#{{ $id }}-{{ $lang }}-content" role="tab" aria-controls="#{{ $id }}-{{ $lang }}-content" aria-selected="true">{{ strtoupper($lang) }}</a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content" id="{{ $id }}-tabContent">
            @foreach($inside_inputs as $lang => $input)
                <div @class(['tab-pane fade show', 'active' => $lang === $current_lang]) id="{{ $id }}-{{ $lang }}-content" role="tabpanel" aria-labelledby="{{ $id }}-{{ $lang }}-tab">
                    <div class="">
                        {!! $input !!}
                    </div>
                    <button type="button" class="btn btn-link" data-click="admin::translate" data-params="{{ $lang }}"><i class="fas fa-language"></i> @lang('admin.translate_field_to_lang', ['name' => $title, 'lang' => strtoupper($lang)])</button>
                </div>
            @endforeach
        </div>
    </div>
    @if($reversed)
        <label for="input_updated_at" @class(['col-sm-' . $label_width => ! $vertical])>{{ $title }}</label>
    @endif
</div>
