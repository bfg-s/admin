<div
    @class(array_merge(['row'], $classes))
    @attributes($attributes)
>
    @if($left_title)
        <div class="col-auto">
            <h4 class="text-secondary">{!! $left_title !!}</h4>
        </div>
    @endif
    @if($center_title)
        <div class="col">
            <hr>
        </div>
        <div class="col-auto">
            <h4 class="text-secondary">{!! $center_title !!}</h4>
        </div>
        <div class="col">
            <hr @class(['mt-0' => ! $anyTitle])>
        </div>
    @else
        <div class="col">
            <hr @class(['mt-0' => ! $anyTitle])>
        </div>
    @endif
    @if($right_title)
        <div class="col-auto">
            <h4 class="text-secondary">{!! $right_title !!}</h4>
        </div>
    @endif
</div>
