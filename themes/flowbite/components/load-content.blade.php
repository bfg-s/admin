<div @attributes($attributes)>
    <div class="overlay">
        <i @class(['fas fa-2x fa-sync-alt', 'fa-spin' => true])></i>
    </div>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</div>
