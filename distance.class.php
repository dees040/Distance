<?php

define("ERROR", false);
define("METER", 1);
define("KM", 2);
define("MILES", 3);
define("YARD", 4);

class Distance
{
    public $type;
    public $round = 2;

    public function __construct($type) {
        $this->type = $type;
    }

    public function get_distance($from, $to) {
        $retval = $this->get_info_between_points($from, $to);
        if ($retval === ERROR) {
            return "<strong>An error has shown up</strong>";
        }

        return $this->distanceToType($retval->Directions->Distance->meters);
    }

    private function distanceToType($distance) {
        switch($this->type) {
            case 1:
                return round($distance, $this->round);
                break;
            case 2:
                return round(($distance / 100), $this->round);
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

    private function get_info_between_points($from, $to) {
        $data = array (
            'output=json',
            'gl=nl',
            'q=' . urlencode('from: '.$from.' to: '.$to)
        );
        $url = 'http://google.com/maps/nav?' . join('&', $data);

        $ch = curl_init( $url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_REFERER, 'http://google.com');

        $jsonString = curl_exec($ch);

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200 ) {

            if (json_decode($jsonString) != NULL) return json_decode($jsonString);

            $input = iconv('UTF-8', 'UTF-8//IGNORE', utf8_encode($jsonString));

            if (json_decode($input) != NULL) return json_decode($input);

            return ERROR;
        } else {
            return ERROR;
        }
    }
}