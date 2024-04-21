<div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch flex-column">
    <div class="card bg-light d-flex flex-fill">
        <div class="card-header text-muted border-bottom-0"></div>
        <div class="card-body pt-0">
            <div class="row">
                <div @class(['col-7' => $avatarField, 'col-12' => !$avatarField])>
                    @if($titleField)
                        <h2 class="lead">
                            <b>{!! is_string($titleField) ? multi_dot_call($model, $titleField) : (is_callable($titleField) ? call_user_func($titleField, $model) : $titleField) !!}</b>
                        </h2>
                    @endif
                    @if($subtitleField)
                        <p class="text-muted text-sm">{!! is_string($subtitleField) ? multi_dot_call($model, $subtitleField) : (is_callable($subtitleField) ? call_user_func($subtitleField, $model) : $subtitleField) !!}</p>
                    @endif
                    <ul @class(['mb-0 fa-ul text-muted', 'ml-4'])>
                        @foreach($rows as $row)
                            @if(! $row['hide'] && $row['field'] !== $avatarField && $row['field'] !== $titleField && $row['field'] !== $subtitleField)
                                <li class="small">
                                    @if($row['icon'])
                                        <span class="fa-li">
                                            <i class="fa-lg {{ $row['icon'] }}"></i>
                                        </span>
                                    @endif
                                    @if($row['label']) {{ $row['label'] }}: @endif {!! $row['value'] !!}
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                @if($avatarField)
                    <div class="col-5 text-center">
                        @php $image = is_string($avatarField) ? multi_dot_call($model, $avatarField) : (is_callable($avatarField) ? call_user_func($avatarField, $model) : $avatarField); @endphp
                        <img
                            src="{{ asset($image) }}"
                            alt="user-avatar"
                            class="img-circle img-fluid"
                            data-click='fancy::img'
                            data-params='{{ asset($image) }}'
                        />
                    </div>
                @endif
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-6 text-left pl-3 pt-1">
                    @if($checkBox)
                        {!! call_user_func($checkBox, $model) !!}
                    @endif
                </div>
                <div class="col-6 text-right">
                    @foreach($buttons as $button)
                        {!! is_callable($button) ? call_user_func($button, $model) : $button !!}
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
