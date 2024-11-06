@if($write)
    <div
        class="modal-content"
        data-modal-name="{{ $modalName }}"
        @attributes($attributes)
    >
        <div class="modal-header">
            <h5 class="modal-title">{!! __($title) ?: '&nbsp;' !!}</h5>
            <a href="javascript:void(0)" class="refresh_modal" style="padding: 10px 15px 0 0;">
                <span style="font-size: 21px;">⟳</span>
            </a>
            <a href="javascript:void(0)" class="close" style="margin-left: 8px; padding-left: 0">
                <span aria-hidden="true">&times;</span>
            </a>
        </div>
        @foreach($contents as $content)
            {!! $content !!}
        @endforeach
        @if(count($left_footer_buttons) || count($center_footer_buttons) || count($footer_buttons))
            <div class="modal-footer">
                <div class="row">
                    <div class="col-auto text-left">
                        @foreach($left_footer_buttons as $footer_button)
                            {!! $footer_button !!}
                        @endforeach
                    </div>
                    <div class="col-auto text-center">
                        @foreach($center_footer_buttons as $center_button)
                            {!! $center_button !!}
                        @endforeach
                    </div>
                    <div class="col-auto text-right">
                        @foreach($footer_buttons as $footer_button)
                            {!! $footer_button !!}
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
@else
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
@endif
