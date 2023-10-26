<template @attributes($attributes) @class($classes)>
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
</template>
