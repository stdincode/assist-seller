<?php

namespace App\Exceptions;

use App\Constants\Errors;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        HttpException::class,
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
    }


    protected function convertExceptionToArray(Throwable $e)
    {
        $error = $this->getProductionError($e);

        if (config('app.debug')) {
            $error->setData('debug', [
                'message'   => $e->getMessage(),
                'exception' => get_class($e),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
                'trace'     => collect($e->getTrace())->map(function ($trace) {
                    return Arr::except($trace, ['args']);
                })->all(),
            ]);
        }

        return $error->toArray();
    }

    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        return response()->json($this->convertExceptionToArray($e), 422);
    }

    /**
     * Всегда возвращать ответ в формате JSON
     *
     * @param $request
     * @param Throwable $e
     * @return bool
     */
    protected function shouldReturnJson($request, Throwable $e)
    {
        return true;
    }

    private function shouldReturnOriginalCode(Throwable $e): bool
    {
        return $e instanceof ClientResponseCodeInterface;
    }

    private function shouldReturnOriginalMessage(Throwable $e): bool
    {
        return $e instanceof ClientResponseMessageInterface;
    }

    private function isValidationException(Throwable $e): bool
    {
        return $e instanceof ValidationException;
    }

    private function getProductionError(Throwable $e): Error
    {
        $error = new Error(Errors::CODE_INTERNAL_ERROR, 'Internal Server Error');

        if ($this->isHttpException($e)) {
            $error->setCode($e->getCode());
            $error->setMessage($e->getMessage());
        }

        if ($this->isValidationException($e)) {
            $error->setCode(Errors::CODE_REQUEST_VALIDATION);
            $error->setMessage('Некорректный запрос');
            $error->setData('validation', $e->errors());
        }

        if ($this->shouldReturnOriginalCode($e)) {
            $error->setCode($e->getCode());
        }

        if ($this->shouldReturnOriginalMessage($e)) {
            $error->setMessage($e->getMessage());
        }

        return $error;
    }
}
