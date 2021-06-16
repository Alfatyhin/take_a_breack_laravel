<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    use HasFactory;

    public static function addLog($name, $data)
    {
        $webhookLog = new WebhookLog();
        $webhookLog->name = $name;
        $webhookLog->data = json_encode($data);
        $webhookLog->save();
    }
}
