<?php

function getIPAddress() {

     $ip = $_SERVER['REMOTE_ADDR'];

     return $ip;
}



function getUrl() {

    $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    $url = explode('?', $url);

    $url = $url[0];


    return $url;
}





function getQuest($url)
{

    $curl = curl_init();

    curl_setopt_array($curl, [

        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "Accept: application/json"
        ],
    ]);

    $response = curl_exec($curl);

    $err = curl_error($curl);

    curl_close($curl);


    return json_decode($response, true);

}

