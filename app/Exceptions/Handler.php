<?php

namespace App\Exceptions;

use App\Models\WebhookLog;
use Carbon\Carbon;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {

        });
//        $this->renderable(function (InvalidOrderException $e, $request) {
//            dd('$e', $request);
//            return response()->view('errors.invalid-order', [], 500);
//        });
    }

    public function render($request, Throwable $exception)
    {
        if ($this->isHttpException($exception)) {
            /** @var HttpExceptionInterface $exception */
            if ($exception->getStatusCode() == 404) {
                return redirect(route('404'));
            }
        }
        if (Auth::guest())  {

            $file = $exception->getFile();
            $file = str_replace('/home/l98123/public_html', ' ', $file);
            $line = $exception->getLine();
            $message = $exception->getMessage();
            $url = $request->getUri();
            $ip = $request->getClientIp();
            $route_name = Route::currentRouteName();

            $date = new Carbon();
            $date_str = $date->format('Ymd-His');
            $message = "<b style='color:brown'>Error #$date_str</b>($message) - $file -- $line <br><b>| $url |</b><br><b>$ip</b><br><b>$route_name</b><br>";

            $lang = 'en';
            $post = $request->post();
            if (isset($post['lang'])) {
                $lang = $post['lang'];
            }

            WebhookLog::addLog($message, $request->post());

            $message_user = "<b style='color:brown'>Error #$date_str</b><br>";

            session()->flash('shop_error_message', $message_user);

            return redirect(route('shop_error', ['lang' => $lang]));

        }

        return parent::render($request, $exception);
    }
}
