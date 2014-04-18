<?php
    require_once('distance.class.php');

    $distance = new Distance(MILES);

    $miles = $distance->get_distance("5643 JR, Eindhoven, The Netherlands", "3068 SN, Rotterdam, The Netherlands");

    var_dump($miles); // float(74.46)