<?php
/**
 * distance.class.php
 * Distance - A simple class for calculating distance between addresses
 *
 * @author      Dees Oomens (dees040)
 * @git         https://github.com/dees040/Distance
 * @version     V0.1
 */

// Define some things
define("ERROR", false);
define("METER", 1);
define("KM", 2);
define("MILES", 3);
define("YARD", 4);

class Distance
{
    public $type;        // Holds the type (1, 2, 3 or 4)
    private $round = 2;  // Holds number to round down to

    /**
     * Class constructor
     * @param $type
     * The type that need to converted to (METER, KM, MILES or YARD)
     */
    public function __construct($type = METER) {
        $this->type = $type;
    }

    /**
     * Function that calculates the distance between to given addresses
     * @param $from
     * The from address
     * @param $to
     * The to address
     * @return bool|float|string
     * Returns an error string when an error has showed up in get_info_between_points
     * Returns an float on success
     * Returns false on wrong $type
     */
    public function get_distance($from, $to) {
        $retval = $this->get_info_between_points($from, $to);

        if ($retval === ERROR) {
            return "<strong>An error has shown up</strong>";
        } else if ($retval->Status->code == 602) {
            return "<strong>Wrong address</strong>";
        }

        return $this->distanceToType($retval->Directions->Distance->meters);
    }

    /**
     * Function that converts meters in given type
     * @param $distance
     * the distance in meters
     * @return bool|float
     */
    private function distanceToType($distance) {
        switch($this->type) {
            case 1:
                return round($distance, $this->round);
                break;
            case 2:
                return round(($distance / 1000), $this->round);
                break;
            case 3:
                return round(($distance * 0.00062137), $this->round);
                break;
            case 4:
                return round(($distance * 1.0936), $this->round);
                break;
            default:
                return ERROR;
                break;
        }
    }

    /**
     * Function that gets all info from start to end point
     * @param $from
     * the string with start point (from)
     * @param $to
     * the string with end point (to)
     * @return bool|mixed
     */
    private function get_info_between_points($from, $to) {
        $data = array (
            'output=json',
            'gl=nl',
            'q=' . urlencode('from: '.$from.' to: '.$to)
        );
        $url = 'http://google.com/maps/nav?' . join('&', $data);

        $ch = curl_init($url); // Create cURL

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_REFERER, 'http://google.com');

        $jsonString = curl_exec($ch);

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200 ) {

            // Check if is valid JSON
            if (json_decode($jsonString) != NULL) return json_decode($jsonString);

            // If is not valid JSON try to giva a UTF-8 encoding
            $input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($jsonString));

            // Another check if is valid JSON
            if (json_decode($input) != NULL) return json_decode($input);

            return ERROR; // When the JSON is not valid JSON return false
        } else {
            return ERROR; // An error has showed up, return false
        }
    }

    /**
     * Function that sets the $round variable of class
     * @param $round int
     * @return bool
     */
    public function round($round) {
        if ($round > 10) return false;
        if ($round < 0) return false;
        if (!is_int($round)) return false;

        $this->round = $round;
        return true;
    }
}