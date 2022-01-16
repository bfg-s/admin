<canvas id="{!! $element !!}" width="{!! $size['width'] !!}" height="{!! $size['height'] !!}">
    <script>
        var chartInitor = function() {
            var ctx = document.getElementById("{!! $element !!}");
                window.{!! $element !!} = new Chart(ctx, {
                    type: '{!! $type !!}',
                    data: {
                        labels: {!! json_encode($labels) !!},
                        datasets: {!! json_encode($datasets) !!}
                    },
                    @if(!empty($optionsRaw))
                    options: {!! $optionsRaw !!}
                        @elseif(!empty($options))
                        options: {!! json_encode($options) !!}
                    @endif
            });
        };
    @if($isNotAjax)
        document.addEventListener("DOMContentLoaded", chartInitor);
    @else
        chartInitor();
    @endif
    </script>
</canvas>
