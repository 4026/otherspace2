<?php

namespace OtherSpace2\Exceptions;

use App;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        //Translate exceptions into JSON for ajax requests.
        if ($request->ajax() || $request->wantsJson()) {
            //Hide the exception message in production environments.
            $message = App::environment('local') ? $e->getMessage() : get_class($e);

            if ($e instanceof HttpException) {
                return response()->json(['error' => $message], $e->getStatusCode());
            } elseif ($e instanceof ModelNotFoundException) {
                return response()->json(['error' => $message], 404);
            } elseif ($e instanceof AuthenticationException) {
                return response()->json(['error' => $message], 403);
            } elseif ($e instanceof AuthorizationException) {
                return response()->json(['error' => $message], 401);
            } elseif ($e instanceof ValidationException && $e->getResponse()) {
                return response()->json(['error' => $message], 422);
            } else {
                return response()->json(['error' => $message], 500);
            }
        }

        return parent::render($request, $e);
    }
}
