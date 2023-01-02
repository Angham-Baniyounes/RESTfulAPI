<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Traits\ApiResponser;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Database\QueryException;
class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
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
            //
        });
    }

    public function report(Throwable $exception) {
        parent::report($exception);
    }

    public function render($request, Throwable $exception) {
        if($exception instanceof  ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        if($exception instanceof ModelNotFoundException) {
            $modelName = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse("Does not exists any {$modelName} with the specified identificator", 404);
        }

        if($exception instanceof  AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }

        if($exception instanceof  AuthorizationException) {
            return $this->errorResponse($exception->getMessage(), 403);
        }

        if($exception instanceof  MethodNotAllowedHttpException) {
            return $this->errorResponse('The specified method for the requests is invalid', 405);
        }

        if($exception instanceof  NotFoundHttpException) {
            return $this->errorResponse('The specified URL cannot be found', 403);
        }

        if($exception instanceof  HttpException) {
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        }

        if($exception instanceof QueryException) {
            $errorCode = $exception->errorInfo[1];

            if($errorCode == 1451) {
                return $this->errorResponse('Cannot remove this resource permanently.  It is related with any other resource', 409);
            }
        }

        if(config('app.debug')) {
            return parent::render($request, $exception);
        }

        return $this->errorResponse('Unexpected Exception. Try later.', 500);


    }

    protected function unauthenticated($request, AuthenticationException $exception) {
        return $this->errorResponse('Unauthenticated.', 401);
        if($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('login');
    }

    protected function  convertValidationExceptionToResponse(ValidationException $e, $request) {
        $errors = $e->validator->errors()->getMessages();

        return $this->errorResponse($errors, 422);
    }
}
