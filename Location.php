<?php

class Location {

    public $coordinates;
    public $geocode_response = null;
    public $geocode_response_raw;
    public $location;

    public function __construct($location) {
        $this->location = $location;
        $this->geocode($location);
        $this->coordinates = new Coordinates(
            $this->geocode_response['results'][0]['geometry']['location']['lat'],
            $this->geocode_response['results'][0]['geometry']['location']['lng']
        );
    }

    private function geocode() {
        $location = urlencode($this->location);
        $retries = 0;

        while((is_null($this->geocode_response) || $this->geocode_response['status'] == 'OVER_QUERY_LIMIT') && $retries < 5) {
            sleep($retries == 0 ? 0 : 1);
            $this->google_geocode($location);
            $retries++;
        }
    }

    private function google_geocode($address) {
        $this->geocode_response_raw = file_get_contents(
            "http://maps.googleapis.com/maps/api/geocode/json?address=$address&sensor=false&region=us"
        );

        $this->geocode_response = json_decode($this->geocode_response_raw, true);
    }
}

class Coordinates {

    public $latitude;
    public $longitude;

    function __construct($latitude, $longitude) {
        $this->latitude = floatval($latitude);
        $this->longitude = floatval($longitude);
    }
}