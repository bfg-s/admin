<div class="clearfix mb-0" id="{{ $id }}" data-inputable @attributes($attributes)>
    @php $i=0; @endphp
    @foreach($values as $value => $title)
        @php
            $checked = false;
            if (is_array($val) && in_array($value, $val)) {
                $checked = true;
            } elseif ($val == $value) {
                $checked = true;
            }
        @endphp
        <div class="icheck-primary float-left mr-3">
            <input
                type="radio"
                id="{{ $id ? 'checkbox-'.$id.'-'.$i : 'checkbox-'.$i }}"
                name="{{ $name }}[]"
                value="{{ $value }}"
                @checked($checked)
            />
            <label for="{{ $id ? 'checkbox-'.$id.'-'.$i : 'checkbox-'.$i }}">{{ $title }}</label>
        </div>
        @php $i++; @endphp
    @endforeach
</div>
