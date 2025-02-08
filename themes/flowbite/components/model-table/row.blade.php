<tr @attributes($attributes) class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</tr>
