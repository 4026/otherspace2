/**
 * JS for otherspace scanner page.
 */

var MAPTYPE_ID = 'otherspace_style';

/**
 * Process story data returned from the server.
 * @param data
 */
function processStory(data)
{
    $('#p_loadingSpinner').addClass('hidden');
    $('#div_output').removeClass('hidden').hide().fadeIn("slow");

    var $location_panel_body = $('#panel_location').children('.panel-body').empty();
    var $time_panel_body = $('#panel_time').children('.panel-body').empty();

    data['locationText'].forEach(function (paragraph) {
        $('<p>').text(paragraph).appendTo($location_panel_body);
    });

    data['timeText'].forEach(function (paragraph) {
        $('<p>').text(paragraph).appendTo($time_panel_body);
    });

    //Draw google map
    var map = new google.maps.Map(document.getElementById('map-canvas'), {
        zoom: 15,
        center: {lat: data.location.lat, lng: data.location.long},
        mapTypeControl: false,
        mapTypeControlOptions: { mapTypeIds: [MAPTYPE_ID] },
        mapTypeId: MAPTYPE_ID,
        streetViewControl: false
    });

    var featureOpts = [
        {
            stylers: [
                { hue: '#110066' },
                { visibility: 'simplified' },
                { gamma: 0.2 },
                { weight: 0.5 }
            ]
        },
        {
            elementType: 'labels',
            stylers: [
                { visibility: 'off' }
            ]
        },
        {
            featureType: 'water',
            stylers: [
                { color: '#000000' }
            ]
        }
    ];
    var styledMapOptions = {name: 'Otherspace'};
    var customMapType = new google.maps.StyledMapType(featureOpts, styledMapOptions);
    map.mapTypes.set(MAPTYPE_ID, customMapType);

    //Draw location marker
    var marker = new google.maps.Marker({
        position: new google.maps.LatLng(data.location.lat, data.location.long),
        map: map,
        title: 'You'
    });

    //Draw zone rect
    var rectangle = new google.maps.Rectangle({
        strokeColor: '#140035',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#140035',
        fillOpacity: 0.35,
        map: map,
        bounds: new google.maps.LatLngBounds(
            new google.maps.LatLng(data['location_bounds'][0]['lat'], data['location_bounds'][0]['long']),
            new google.maps.LatLng(data['location_bounds'][1]['lat'], data['location_bounds'][1]['long']))
    });
    map.setCenter(rectangle.getBounds().getCenter());

    // Draw zone info
    var infowindow = new google.maps.InfoWindow({
        content: '<span class="mapInfoText">' + data['locationName'] + '</span>',
        position : new google.maps.LatLng(
            rectangle.getBounds().getNorthEast().lat(),
            rectangle.getBounds().getCenter().lng()
        )
    });
    infowindow.open(map);
}

/**
 * Display the provided error text in an alert on the page.
 * @param error_text
 */
function displayError(error_text)
{
    $('#p_loadingSpinner').addClass('hidden');
    $("<div class='alert alert-danger' role='alert'>" + error_text + "</div>").appendTo('#div_errors');
}

$(function () {
    if (!navigator.geolocation) {
        displayError("Geolocation is not supported by your browser.");
    }

    $(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
        displayError("Encountered some interference: " + thrownError);
    });

    $('#button_scan').click(function () {
        $('#p_loadingSpinner').removeClass('hidden');
        $('#div_output').addClass('hidden');

        navigator.geolocation.getCurrentPosition(
            function success(position) {
                $.post('getStory.php', {lat: position.coords.latitude, long: position.coords.longitude})
                    .done(processStory)
                ;
            },
            function error() { displayError("Unable to discern your location"); },
            {
                enableHighAccuracy: true,
                maximumAge: 10000,
                timeout: 10000
            }
        );
    });


});