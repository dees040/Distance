<?php

define("ERROR", false);
define("KM", "meters");
define("MILES", "feet");

class Distance
{

    private $type;

    public function __constructor($type) {
        $this->type = $type;
        $this->init();
    }

    private function init() {

    }

    private function distance($from, $to) {
        $retval = $this->get_info_between_points($from, $to);
        return round($retval->Directions->Distance->meters/1000,2);
    }

    private function get_info_between_points($from, $to) {
        $data = array (
            'output=json',
            'gl=nl',
            'q=' . urlencode('from: '.$from.' to: '.$to)
        );
        $url = 'http://google.com/maps/nav?' . join('&', $data);

        $ch = curl_init( $url);

        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt( $ch, CURLOPT_REFERER, 'http://google.com');

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