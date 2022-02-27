<?php
include("functions.php");

if (!empty($_GET['getgeo'])) {
    $ip = getIPAddress();

    // заглушка для локального теста
    if ($ip == '127.0.0.1') {
        $ip = '1.47.255.255';
    }

    $urlApi = "http://www.geoplugin.net/json.gp?ip=$ip";
    $info = getQuest($urlApi);

    $data['ip'] = $ip;
    $data['country'] = $info['geoplugin_countryCode'];
    $data['custom4'] = $info['geoplugin_countryName'];

    echo json_encode($data);

}
