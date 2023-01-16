<?php


namespace App\Exceptions;
use Illuminate\Contracts\Container\Container;
use Exception;
use Throwable;

class InvalidOrderException extends Exception
{


    public function render($message, $data)
    {
        dd('test', $message, $data);

    }
}