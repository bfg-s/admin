<ol class="breadcrumb p-1 pl-2 ml-1 m-0 bg-white">
    <li class="breadcrumb-item active"><i class="fas fa-search"></i> @lang('admin.sort_result_report')</li>
    @foreach($fields as $field)
        @php
            $formGroup = $field['class'];
            $val = request($formGroup->get_path());
            $val = is_array($val) ? implode(' - ', $val) : $val;
        @endphp
        @if($val)
            <li class="breadcrumb-item">
                <a href="{{ urlWithGet([], [$formGroup->get_path()]) }}" title="{{ __('admin.cancel') . ': ' . $formGroup->get_title() }}">
                    <i class="fas fa-window-close text-danger"></i>
                </a>
                {{ $formGroup->get_title().': '.$val }}
            </li>
        @endif
    @endforeach
</ol>
