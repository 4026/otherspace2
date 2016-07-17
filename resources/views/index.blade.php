@extends('layouts.app')

@section('content')
<div class="container">

    <div class="text-center" style="padding: 40px 0;">
        <button type="button" id="button_scan" class="btn btn-primary btn-lg">
            <span class="glyphicon glyphicon-dashboard"></span>
            Scan the otherspace
        </button>
    </div>

    <p id="p_loadingSpinner" class="text-center hidden"><img src="img/loading_spinner.gif" /></p>

    <div id="div_errors"></div>

    <div id="div_output" class="hidden">
        <div class="row">
            <div class="col-md-6">
                <div id="panel_location" class="panel">
                    <div class="panel-body">
                    </div>
                </div>

                <div id="panel_time" class="panel">
                    <div class="panel-body">
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div id="panel_map" class="panel">
                    <div class="panel-body">
                        <div id="map-canvas" style="height: 400px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>



</div>
@endsection


@section('scripts')
    <!-- JS libraries -->
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyD5WmIuY8JHfodthQ6evZDsmUYJbG2NttA"></script>
    <!-- Page scripts -->
    <script src="{{ elixir('js/app.js') }}"></script>
@endsection
