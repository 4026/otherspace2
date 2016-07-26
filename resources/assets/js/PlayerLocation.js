/**
 * Singleton class for tracking the player's current location.
 */
function PlayerLocation()
{
    if (PlayerLocation._instance) {
        throw "Only one PlayerLocation instance may exist at once.";
    }

    //Check for geolocation functionality.
    if (!navigator.geolocation) {
        displayError("Geolocation is not supported by your browser.");
    }

    this.latitude = null;
    this.longitude = null;

    this.previous_position = null;

    /**
     * Get the player's location a Google Maps LatLng object.
     * @returns {google.maps.LatLng}
     */
    this.asLatLng = function() {
        return new google.maps.LatLng(this.latitude, this.longitude);
    };

    /**
     * Set the location values in this instance.
     * @param latitude
     * @param longitude
     */
    this.set = function(latitude, longitude) {
        this.latitude = latitude;
        this.longitude = longitude;
    };

    /**
     * Called when the player's position changes.
     * @param positionUpdateCallback
     * @param position
     */
    this.onPositionUpdate = function(positionUpdateCallback, position) {
        this.set(position.coords.latitude, position.coords.longitude);

        // Only trigger the callback function if the player's location has changed significantly since it was last run.
        if (this.previous_position == null
            || google.maps.geometry.spherical.computeDistanceBetween(this.previous_position, this.asLatLng()) > 20) {

            this.previous_position = this.asLatLng();
            positionUpdateCallback();
        }
    };

    /**
     * Start watching the player's location.
     */
    this.startTracking = function(positionUpdateCallback) {

        navigator.geolocation.watchPosition(
            this.onPositionUpdate.bind(this, positionUpdateCallback),
            function error(e) {
                displayError("Unable to discern your location: " + e.message);
            },
            {
                enableHighAccuracy: true,
                maximumAge: 10000,
                timeout: 10000
            }
        );
    };


}

/**
 * Singleton implementation.
 * @returns {PlayerLocation}
 */
PlayerLocation.instance = function() {
    if (!PlayerLocation._instance) {
        PlayerLocation._instance = new PlayerLocation();
    }
    return PlayerLocation._instance;
};