@extends('layouts.app')

@section('content')

<div id="map-container">
    <div id="map-canvas"></div>
</div>

<div id="div-errors"></div>

<div class="container-fluid">

    <div class="row">
        <div class="col-lg-3 col-lg-offset-0 col-xs-10 col-xs-offset-1">
            <div id="panel-location" class="panel">
                <div class="panel-heading">
                    <span id="region-loading">
                        <i class="fa fa-spinner fa-pulse fa-fw"></i>
                        Probing...
                    </span>
                    <a id="region-name" role="button" class="hidden" data-toggle="collapse" href="#region-detail">
                        <i class="fa fa-fw fa-caret-right"></i>
                        <span></span>
                    </a>
                </div>
                <div id="region-detail" class="panel-collapse collapse">
                    <div class="panel-body">
                        <div id="region-detail-text"></div>

                        <hr />

                        {{-- Add message form --}}
                        <div id="react_messageComposer"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection


@section('scripts')

    <!-- JS libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/15.2.1/react-with-addons.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/15.2.1/react-dom.min.js" crossorigin="anonymous"></script>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyD5WmIuY8JHfodthQ6evZDsmUYJbG2NttA"></script>

    <!-- Exported data -->
    <script type="application/javascript">
        var message_grammar = {!! file_get_contents(base_path('/resources/assets/json/message_grammar.json')) !!};
    </script>

    <!-- Page scripts -->
    <script src="{{ elixir('js/app.js') }}"></script>

@endsection
