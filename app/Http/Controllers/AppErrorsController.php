<?php


namespace App\Http\Controllers;


use App\Models\AppErrors;

class AppErrorsController
{

    public function index()
    {
        $appErrors = AppErrors::orderBy('id', 'DESC')->paginate(20);



        return view('app.errors', [
            'appErrors' => $appErrors
        ]);
    }

}
