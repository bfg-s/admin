<table
    @class(array_merge(['table', 'table-sm', 'table-hover', 'table-' . $type => ! $hasHeader && $type], $classes))
    @attributes($attributes)
>
    @if($hasHeader)
        <thead @class(['thead-' . $type => $type])>
            <tr>
                @foreach($this->array_build['headers'] as $header)
                    <th scope="col">{!! $header !!}</th>
                @endforeach
            </tr>
        </thead>
    @endif
    <tbody>
        @php
            $simple = false;
        @endphp
        @foreach($rows as $key => $row)
            <tr>
                @if(is_array($row) && ! $simple)
                    @foreach(array_values($row) as $ki => $col)
                        @if(!$ki && $first_th)
                            <th scope="row">{!! $col !!}</th>
                        @else
                            <td>{!! $col !!}</td>
                        @endif
                    @endforeach
                @else
                    @php
                        $simple = true;
                    @endphp
                    @if($first_th)
                        <th scope="row">{{ $key }}</th>
                    @else
                        <td>{{ $key }}</td>
                    @endif
                    @if(!is_array($row))
                        @if(is_callable($row))
                            <td>{!! call_user_func($row, $key) !!}</td>
                        @else
                            <td>{!! $row !!}</td>
                        @endif
                    @endif
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
