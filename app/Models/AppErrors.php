<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppErrors extends Model
{
    use HasFactory;

    public static function addError($name, $message)
    {
        $errorData = new AppErrors();
        $data[$name] = $message;
        $errorData->error = json_encode($data);
        $errorData->save();

        return $data;
    }
}
