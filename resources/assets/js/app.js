/**
 * JS for otherspace scanner page.
 */

var MAPTYPE_ID = 'otherspace_style';

/**
 * Update the app display based on the player's current location.
 */
function updateLocation() {
    $('#p_loadingSpinner').removeClass('hidden');
    $('#div_output').addClass('hidden');

    navigator.geolocation.getCurrentPosition(
        function success(position) {
            $.get('/location', {latitude: position.coords.latitude, longitude: position.coords.longitude})
                .done(processStory)
            ;
        },
        function error() {
            $.get('/location', {latitude: 51.4623428, longitude: -0.1759524})
                .done(processStory)
            ;
        },
        {
            enableHighAccuracy: true,
            maximumAge: 10000,
            timeout: 10000
        }
    );
}

function attachMessage(message_text, message_marker, map) {
    var message_info = new google.maps.InfoWindow({
        content: '<span class="mapInfoText">' + message_text + '</span>'
    });

    message_marker.addListener('click', function () {
        message_info.open(map, message_marker);
    });
}


/**
 * Process story data returned from the server.
 * @param data
 */
function processStory(data) {
    $('#p_loadingSpinner').addClass('hidden');
    $('#div_output').removeClass('hidden').hide().fadeIn("slow");

    var $location_panel_body = $('#panel_location').children('.panel-body').empty();
    var $time_panel_body = $('#panel_time').children('.panel-body').empty();

    data['area']['locationText'].forEach(function (paragraph) {
        $('<p>').text(paragraph).appendTo($location_panel_body);
    });

    data['area']['timeText'].forEach(function (paragraph) {
        $('<p>').text(paragraph).appendTo($time_panel_body);
    });

    //Draw google map
    var map = new google.maps.Map(document.getElementById('map-canvas'), {
        zoom: 15,
        center: {lat: data.player.lat, lng: data.player.long},
        mapTypeControl: false,
        mapTypeControlOptions: {mapTypeIds: [MAPTYPE_ID]},
        mapTypeId: MAPTYPE_ID,
        streetViewControl: false
    });

    var featureOpts = [
        {
            stylers: [
                {hue: '#110066'},
                {visibility: 'simplified'},
                {gamma: 0.2},
                {weight: 0.5}
            ]
        },
        {
            elementType: 'labels',
            stylers: [
                {visibility: 'off'}
            ]
        },
        {
            featureType: 'water',
            stylers: [
                {color: '#000000'}
            ]
        }
    ];
    var styledMapOptions = {name: 'Otherspace'};
    var customMapType = new google.maps.StyledMapType(featureOpts, styledMapOptions);
    map.mapTypes.set(MAPTYPE_ID, customMapType);

    //Draw location marker
    var player_marker = new google.maps.Marker({
        position: new google.maps.LatLng(data.player.lat, data.player.long),
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
            new google.maps.LatLng(data['area']['location_bounds'][0]['lat'], data['area']['location_bounds'][0]['long']),
            new google.maps.LatLng(data['area']['location_bounds'][1]['lat'], data['area']['location_bounds'][1]['long']))
    });
    map.setCenter(rectangle.getBounds().getCenter());

    // Draw zone info
    var area_info = new google.maps.InfoWindow({
        content: '<span class="mapInfoText">' + data['area']['locationName'] + '</span>',
        position: new google.maps.LatLng(
            rectangle.getBounds().getNorthEast().lat(),
            rectangle.getBounds().getCenter().lng()
        )
    });
    area_info.open(map);

    //Add message markers
    for(var i = 0; i < data.area.messages.length; ++i) {
        var message = data.area.messages[i];
        var message_marker = new google.maps.Marker({
            position: new google.maps.LatLng(message.latitude, message.longitude),
            map: map,
            title: 'Message',
            icon: {
                url: 'https://files.4026.me.uk/otherspace/marker-icons/message.png',
                anchor: new google.maps.Point(16, 16)
            }
        });

        attachMessage(message.body_text, message_marker, map);
    }
}

/**
 * Display the provided error text in an alert on the page.
 * @param error_text
 */
function displayError(error_text) {
    $('#p_loadingSpinner').addClass('hidden');
    $("<div class='alert alert-danger' role='alert'>" + error_text + "</div>").appendTo('#div_errors');
}

/**
 * Get the text of a message clause from the object describing its structure.
 * @param clause
 * @returns {string}
 */
function getClauseText(clause) {
    var clause_text = window.message_grammar.clauses[clause.type];
    if (clause.word_list != null && clause.word != null) {
        clause_text = clause_text.replace('____', window.message_grammar.words[clause.word_list][clause.word]);
    }
    return clause_text;
}

/**
 * Get the text of a message from the object describing its structure.
 * @param message
 * @returns {string}
 */
function getMessageText(message) {
    var message_text = "";

    if (message.clause_1.type != null) {
        message_text += getClauseText(message.clause_1);
    }
    if (message.conjunction != null) {
        message_text += window.message_grammar.conjunctions[message.conjunction];
    }
    if (message.clause_2.type != null) {
        message_text += getClauseText(message.clause_2);
    }

    return message_text.ucfirst();
}


/**
 * document.ready functions.
 */
$(function () {
    //Check for geolocation functionality.
    if (!navigator.geolocation) {
        displayError("Geolocation is not supported by your browser.");
    }

    //Set up ajax options and error handling.
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ajaxError(function (event, jqXHR, ajaxSettings, thrownError) {
        displayError("Encountered some interference: " + thrownError);
    });


    //Bind button events.
    $('#btn_scan').click(updateLocation);


    $('#btn_etch').click(function () {
        var message = $('#input_message').val();

        navigator.geolocation.getCurrentPosition(
            function success(position) {
                var data = {latitude: position.coords.latitude, longitude: position.coords.longitude, message: message};
                $.post('/message', data)
                    .done(updateLocation);
            },
            function error() {
                $.post('/message', {latitude: 51.4623428, longitude: -0.1759524, message: message})
                    .done(updateLocation)
                ;
            },
            {
                enableHighAccuracy: true,
                maximumAge: 10000,
                timeout: 10000
            }
        );

    });

});