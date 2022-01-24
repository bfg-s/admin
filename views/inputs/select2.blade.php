<select x-ref="select" class="form-control select2bs4">
    @foreach($options as $value => $title)
        <option value="{{ $value }}" {{ $value == $default ? 'selected' : '' }} >{{ $title }}</option>
    @endforeach
</select>
