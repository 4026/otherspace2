/**
 * JS for otherspace scanner page.
 */

var MAPTYPE_ID = 'otherspace_style';

/**
 * Update the app display based on the player's current position.
 */
function updateLocation() {
    //Collapse region details
    $('#region-name').collapse('hide').addClass('hidden');

    //Show loading spinner
    $('#region-loading').removeClass('hidden');

    var parameters = {latitude: PlayerLocation.instance().latitude, longitude: PlayerLocation.instance().longitude};
    $.get('/location', parameters).done(processStory);
}

/**
 * Attach an InfoWindow that shows on click with the provided text to the provided marker on the provided map.
 * @param message_text
 * @param message_marker
 * @param map
 */
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
    //Hide loading spinner
    $('#region-loading').addClass('hidden');

    //Update and show region name
    $('#region-name')
        .removeClass('hidden')
        .children('span')
        .text(data['region']['locationName']);


    var $location_panel_body = $('#region-detail-text').empty();
    data['region']['locationText'].forEach(function (paragraph) {
        $('<p>').text(paragraph).appendTo($location_panel_body);
    });

    //Draw google map
    var map = new google.maps.Map(document.getElementById('map-canvas'), {
        zoom: 16,
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

    //Draw player location marker
    var player_marker = new google.maps.Marker({
        position: PlayerLocation.instance().asLatLng(),
        map: map,
        title: 'You',
        icon: {
            url: 'https://files.4026.me.uk/otherspace/marker-icons/player.png',
            anchor: new google.maps.Point(16, 16)
        }
    });

    //Draw player interaction circle
    var player_circle = new google.maps.Circle({
        strokeColor: '#FFFFFF',
        strokeOpacity: 0.8,
        strokeWeight: 1,
        fillColor: '#FFFFFF',
        fillOpacity: 0.35,
        map: map,
        center: PlayerLocation.instance().asLatLng(),
        radius: window.environment.item_marker_collect_radius
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
            new google.maps.LatLng(data['region']['location_bounds'][0]['lat'], data['region']['location_bounds'][0]['long']),
            new google.maps.LatLng(data['region']['location_bounds'][1]['lat'], data['region']['location_bounds'][1]['long']))
    });
    map.setCenter(rectangle.getBounds().getCenter());

    //Add message markers
    var i, position;
    for (i = 0; i < data.region.messages.length; ++i) {
        var message = data.region.messages[i].message;
        position = data.region.messages[i].position;
        var message_marker = new google.maps.Marker({
            position: new google.maps.LatLng(position.latitude, position.longitude),
            map: map,
            title: 'Message',
            icon: {
                url: 'https://files.4026.me.uk/otherspace/marker-icons/message.png',
                anchor: new google.maps.Point(16, 16)
            }
        });

        attachMessage(getMessageText(message), message_marker, map);
    }

    //Add item markers
    for (i = 0; i < data.region.item_markers.length; ++i) {
        position = data.region.item_markers[i].position;
        var item_marker = new google.maps.Marker({
            position: new google.maps.LatLng(position.latitude, position.longitude),
            map: map,
            title: 'Item',
            icon: {
                url: 'https://files.4026.me.uk/otherspace/marker-icons/item.png',
                anchor: new google.maps.Point(16, 16)
            },
            item_marker_id: i
        });

        item_marker.addListener('click', function () {
            var parameters = {
                latitude: PlayerLocation.instance().latitude,
                longitude: PlayerLocation.instance().longitude,
                marker_id: this.item_marker_id
            };
            var marker = this;

            $.post('/claim-item', parameters)
                .done(function(data) {
                    alert(data.item.display_name);
                    marker.setMap(null); // Hide the marker
                });
        });
    }
}

/**
 * Display the provided error text in an alert on the page.
 * @param error_text
 */
function displayError(error_text) {
    $("<div class='alert alert-danger' role='alert'>" + error_text + "</div>")
        .appendTo('#div-errors')
        .delay(5000)
        .fadeOut(function () {
            $(this).remove();
        });
}

/**
 * Get the text of a message clause from the object describing its structure.
 * @param clause
 * @returns {string}
 */
function getClauseText(clause) {
    var clause_text = window.environment.message_grammar.clauses[clause.type];
    if (clause.word_list != null && clause.word != null) {
        clause_text = clause_text.replace(
            '____',
            window.environment.message_grammar.words[clause.word_list][clause.word]
        );
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
        message_text += window.environment.message_grammar.conjunctions[message.conjunction];
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

    //Start watching the player's location.
    PlayerLocation.instance().startTracking(updateLocation);

    //Bind an event that changes the caret icon on the region name when the region description is shown or hidden.
    $('#region-detail')
        .on('show.bs.collapse', function () {
            $('#region-name').children('i').removeClass('fa-caret-right').addClass('fa-caret-down');
        })
        .on('hide.bs.collapse', function () {
            $('#region-name').children('i').removeClass('fa-caret-down').addClass('fa-caret-right');
        });
});