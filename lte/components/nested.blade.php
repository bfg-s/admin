<div @class(array_merge(['dd'], $classes)) @attributes($attributes)>
    <ol class="dd-list">
        @foreach($models as $item)
            <li class="dd-item dd3-item" data-id="{{ $item->id }}">
                <div class="dd-handle dd3-handle">
                    <i class="fas fa-arrows-alt"></i>
                </div>
                @php
                    $cc_access = ($controls)($item);
                @endphp
                @if($cc_access || $cc)
                    <div class="float-right m-1">
                        @if($menu)
                            {!! $buttons($item, $cc_access, $cc) !!}
                        @endif
                    </div>
                @endif
                <div class="dd3-content" style="height: auto;min-height: 41px;">
                    @foreach($contents as $content)
                        {!! $content !!}
                    @endforeach
                    @if(is_array($title_field))
                        <span class="text">{!! $title_field !!}</span>
                    @else
                        @if(is_callable($title_field))
                            <span class="text">{!! call_user_func($title_field, $item) !!}</span>
                        @else
                            <span class="text">{!! multi_dot_call($item, $title_field) !!}</span>
                        @endif
                    @endif
                </div>
                @if ($maxDepth > 1)
                    @php $list = $this->model->where($this->parent_field, $item->id); @endphp
                    @if($list->count())
                        @include(admin_template('components.nested'))
                    @endif
                @endif
            </li>
        @endforeach
    </ol>
</div>
