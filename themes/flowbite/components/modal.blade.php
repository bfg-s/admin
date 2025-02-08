@if($write)
    <div
        x-data="{
            isOpen: true,
            openModal() {
                this.isOpen = true;
                document.body.style.overflow = 'hidden';
            },
            closeModal() {
                this.isOpen = false;
                document.body.style.overflow = '';
                exec('modal:destroy', '{{ $modalId }}');
            }
        }"
        x-show="isOpen"
        x-cloak
        x-on:keydown.escape.window="closeModal"
        x-init="openModal()"
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-50 modal-content overflow-y-auto"
        data-modal-name="{{ $modalName }}"
        @attributes($attributes)
    >
        <div
            class="bg-white dark:bg-gray-800 text-gray-500 border border-gray-200 dark:border-gray-700 dark:text-gray-400 rounded-lg shadow-lg w-full max-w-2xl max-h-screen overflow-hidden"
            x-on:click.outside="closeModal"
        >
            <!-- Modal Header -->
            <div class="flex justify-between items-center px-4 py-3">
                <h5 class="text-lg font-semibold">
                    {!! __($title) ?: '&nbsp;' !!}
                </h5>
                <div class="flex items-center space-x-3">
                    <button
                        type="button"
                        class="text-gray-500 hover:text-gray-800 focus:outline-none refresh_modal"
                    >
                        <i class="fas fa-redo"></i>
                    </button>
                    <button
                        type="button"
                        class="text-gray-500 hover:text-gray-800 focus:outline-none close"
                    >
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="max-h-96 overflow-auto p-4">
                @foreach($contents as $content)
                    {!! $content !!}
                @endforeach
            </div>

            <!-- Modal Footer -->
            @if(count($left_footer_buttons) || count($center_footer_buttons) || count($footer_buttons))
                <div class="px-4 py-3">
                    <div class="flex justify-between">
                        <div class="flex space-x-2">
                            @foreach($left_footer_buttons as $footer_button)
                                {!! $footer_button !!}
                            @endforeach
                        </div>
                        <div class="flex justify-center space-x-2">
                            @foreach($center_footer_buttons as $center_button)
                                {!! $center_button !!}
                            @endforeach
                        </div>
                        <div class="flex justify-end space-x-2">
                            @foreach($footer_buttons as $footer_button)
                                {!! $footer_button !!}
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@else
    @foreach($contents as $content)
        {!! $content !!}
    @endforeach
@endif




{{--@if($write)--}}
{{--    <div--}}
{{--        class="modal-content"--}}
{{--        data-modal-name="{{ $modalName }}"--}}
{{--        @attributes($attributes)--}}
{{--    >--}}
{{--        <div class="modal-header">--}}
{{--            <h5 class="modal-title">{!! __($title) ?: '&nbsp;' !!}</h5>--}}
{{--            <a href="javascript:void(0)" class="refresh_modal" style="padding: 10px 15px 0 0;">--}}
{{--                <span style="font-size: 21px;">⟳</span>--}}
{{--            </a>--}}
{{--            <a href="javascript:void(0)" class="close" style="margin-left: 8px; padding-left: 0">--}}
{{--                <span aria-hidden="true">&times;</span>--}}
{{--            </a>--}}
{{--        </div>--}}
{{--        @foreach($contents as $content)--}}
{{--            {!! $content !!}--}}
{{--        @endforeach--}}
{{--        @if(count($left_footer_buttons) || count($center_footer_buttons) || count($footer_buttons))--}}
{{--            <div class="modal-footer">--}}
{{--                <div class="row">--}}
{{--                    <div class="col-auto text-left">--}}
{{--                        @foreach($left_footer_buttons as $footer_button)--}}
{{--                            {!! $footer_button !!}--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                    <div class="col-auto text-center">--}}
{{--                        @foreach($center_footer_buttons as $center_button)--}}
{{--                            {!! $center_button !!}--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                    <div class="col-auto text-right">--}}
{{--                        @foreach($footer_buttons as $footer_button)--}}
{{--                            {!! $footer_button !!}--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        @endif--}}
{{--    </div>--}}
{{--@else--}}
{{--    @foreach($contents as $content)--}}
{{--        {!! $content !!}--}}
{{--    @endforeach--}}
{{--@endif--}}
