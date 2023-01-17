<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class WebhookLog extends Model
{
    use HasFactory;

    public static function addLog($name, $data = false)
    {
        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }
        Log::channel('orders')->info("$name - $data");
    }
}
