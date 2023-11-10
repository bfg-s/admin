<div
    @class(array_merge(['form-group row'], $classes))
    @attributes($attributes)
    data-label-width="{{ $label_width }}"
    data-vertical="@json($vertical)"
>
    @if(!$reversed)
        <label for="input_updated_at" @class(['col-sm-' . $label_width => ! $vertical, 'col-sm-12' => $vertical])>{{ $name }}</label>
    @endif
    <div @class(['col-sm-' . (12 - $label_width) => !$vertical, 'col-sm-12' => $vertical])>

        <ul class="nav nav-tabs" id="{{ $id }}-tab" role="tablist">
            @foreach($insideInputs as $lang => $input)
                <li class="nav-item">
                    <a @class(['nav-link', 'active' => $lang === \Illuminate\Support\Facades\App::getLocale()]) id="{{ $id }}-{{ $lang }}-tab" data-toggle="pill" href="#{{ $id }}-{{ $lang }}-content" role="tab" aria-controls="#{{ $id }}-{{ $lang }}-content" aria-selected="true">{{ strtoupper($lang) }}</a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content" id="{{ $id }}-tabContent">
            @foreach($insideInputs as $lang => $input)
                <div @class(['tab-pane fade show', 'active' => $lang === \Illuminate\Support\Facades\App::getLocale()]) id="{{ $id }}-{{ $lang }}-content" role="tabpanel" aria-labelledby="{{ $id }}-{{ $lang }}-tab">
                    <div class="">
                        {!! $input !!}
                    </div>
                    <button type="button" class="btn btn-link" data-click="admin::translate" data-params="{{ json_encode(['lang' => $lang, 'route' => route('admin.translate')]) }}"><i class="fas fa-language"></i> Translate {{ $name }} to {{ strtoupper($lang) }}</button>
                </div>
            @endforeach
        </div>
    </div>
    @if($reversed)
        <label for="input_updated_at" @class(['col-sm-' . $label_width => ! $vertical, 'col-sm-12' => $vertical])>{{ $name }}</label>
    @endif
</div>
