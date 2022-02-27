<?php

namespace App\Http\Controllers;

use App\Models\WebhookLog;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getIcreditUrl(Request $request)
    {
        $Data = $request->post();
        WebhookLog::addLog('Api iCredit pUrl', $Data);

    }
}
