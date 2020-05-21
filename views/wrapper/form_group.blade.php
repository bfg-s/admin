@php
    $__form_model = $model;
@endphp
@if($vertical)
@vformgroup($set_title,$set_name,$set_icon,$set_info,$set_label_width)
@else
@formgroup($set_title,$set_name,$set_icon,$set_info,$set_label_width)
@endif
{!! $form_group()->field($name,$title,$id,$value,$__has_bug,$path) !!}
@endformgroup