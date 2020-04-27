@cardbodyform()

    @if(gets()->lte->menu->model)

        @foreach(gets()->lte->menu->model->getFillable() as $key)

            @formgroup(Str::title(str_replace('_', ' ', $key)), $key)

                <input type="text" name="{{$name}}" value="{{$value}}" id="{{$id}}" placeholder="{{$title}}" class="form-control" />

            @endformgroup

        @endforeach

    @endif

@endcardbodyform

@formfooter

