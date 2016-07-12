<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Otherspace scanner</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css">

    <!-- Theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootswatch/3.3.5/darkly/bootstrap.min.css">

    <!-- Custom styles -->
    <link rel="stylesheet" href="/css/scanner.css">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

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

<!-- JS libraries -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>

<!-- Page scripts -->
<script type="text/javascript" src="/js/scanner.js"></script>

</body>
</html>

