<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Orders extends Controller
{
    public function getIcreditPaymentUrl()
    {
        echo "<pre>";

        if (!empty($_POST)) {
            var_dump($_POST);
        } else {
            echo "no POST data";
        }
    }
}
