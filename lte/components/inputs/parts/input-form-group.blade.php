<div
    @class(array_merge(['form-group row'], $classes))
    @attributes($attributes)
    data-label-width="{{ $label_width }}"
    data-vertical="@json($vertical)"
>
    @if(!$reversed)
        <label
            for="{{ $id }}"
            @class(['col-sm-' . $label_width => ! $vertical])
        >{{ $title }}</label>
    @endif

    <div @class(['input-group' => $icon, 'w-100' => $vertical, 'col-sm-' . $group_width => !$vertical && $title])>
        @if ($icon)
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="{!! $icon !!}"></i></span>
            </div>
        @endif

        @foreach($contents as $content)
            {!! $content !!}
        @endforeach
    </div>

    @if($reversed)
        <label
            for="{{ $id }}"
            @class(['col-sm-' . $label_width => ! $vertical])
        >{{ $title }}</label>
    @endif

    @if ($info)
        @if($vertical)
            <div class="col-sm-{{ $label_width }}"></div>
        @endif
        <small @class(['text-primary invalid-feedback d-block', 'col-sm-' . $group_width => ! $vertical])>
            <i class="fas fa-info-circle"></i> {!! $info !!}
        </small>
    @endif
    @if($name && $errors && $hasError)
        @foreach($messages as $mess)
            @if(! $vertical)
                <div class="col-sm-{{ $label_width }}"></div>
            @endif
            <small @class(['error invalid-feedback d-block', 'col-sm-' . $group_width => ! $vertical])>
                <span class="fas fa-exclamation-triangle">
                    {!! $mess !!}
                </span>
            </small>
        @endforeach
    @endif
</div>
