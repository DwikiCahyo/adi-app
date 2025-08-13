<?php

namespace App\Exceptions;

use App\Exceptions\TooManySlugAttemptsException as ExceptionsTooManySlugAttemptsException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;


class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        $logLevel = $this->getLogLevel($e);
        Log::log($logLevel, 'Exception caught in render', [
            'type' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'expects_json' => $request->expectsJson(),
        ]);

         if ($e instanceof TooManySlugAttemptsException) {
            return $this->handleTooManyAttemptsException($request, $e);
        }

        if ($e instanceof ModelNotFoundException) {
            return $this->handleModelNotFoundException($request, $e);
        }

         if ($e instanceof NotFoundHttpException) {
            return $this->handleNotFoundHttpException($request, $e);
        }

         if ($e instanceof MethodNotAllowedHttpException) {
            return $this->handleMethodNotAllowedException($request, $e);
        }

        if ($e instanceof ValidationException) {
            return $this->handleValidationException($request, $e);
        }

        if ($e instanceof AuthenticationException) {
            return $this->handleAuthenticationException($request, $e);
        }

        if ($e instanceof AuthorizationException) {
            return $this->handleAuthorizationException($request, $e);
        }

        return parent::render($request, $e);


    }


     /**
     * Handle TooManySlugAttemptsException
     */
    protected function handleTooManyAttemptsException(Request $request, TooManySlugAttemptsException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'Too Many Attempts',
                'message' => $exception->getMessage(),
                'retry_after' => 60
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }
        
        // return response()->view('errors.429', [
        //     'message' => $exception->getMessage(),
        //     'retry_after' => 60
        // ], Response::HTTP_TOO_MANY_REQUESTS);
    }

    /**
     * Handle ModelNotFoundException
     */
    protected function handleModelNotFoundException(Request $request, ModelNotFoundException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'Not Found',
                'message' => 'The requested resource was not found.'
            ], Response::HTTP_NOT_FOUND);
        }
        
         return $this->renderUniversalErrorView($request, Response::HTTP_NOT_FOUND, $exception); 
    }

     /**
     * Handle NotFoundHttpException
     */
    protected function handleNotFoundHttpException(Request $request, NotFoundHttpException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'Not Found',
                'message' => 'The requested endpoint was not found.'
            ], Response::HTTP_NOT_FOUND);
        }
        
        return $this->renderUniversalErrorView($request, Response::HTTP_NOT_FOUND, $exception);
    }

    /**
     * Handle MethodNotAllowedHttpException
     */
    protected function handleMethodNotAllowedException(Request $request, MethodNotAllowedHttpException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'Method Not Allowed',
                'message' => 'The requested method is not allowed for this endpoint.'
            ], Response::HTTP_METHOD_NOT_ALLOWED);
        }
        
        return parent::render($request, $exception);
    }

    /**
     * Handle ValidationException
     */
    protected function handleValidationException(Request $request, ValidationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'Validation Failed',
                'message' => 'The given data was invalid.',
                'errors' => $exception->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        
        return parent::render($request, $exception);
    }

    /**
     * Handle AuthenticationException
     */
    protected function handleAuthenticationException(Request $request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthenticated',
                'message' => 'Authentication is required to access this resource.'
            ], Response::HTTP_UNAUTHORIZED);
        }
        
        return parent::render($request, $exception);
    }


    /**
     * Handle AuthorizationException
     */
    protected function handleAuthorizationException(Request $request, AuthorizationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => 'Forbidden',
                'message' => 'You do not have permission to access this resource.'
            ], Response::HTTP_FORBIDDEN);
        }
        
        return parent::render($request, $exception);
    }

     /**
     * Check if the exception is an HTTP exception
     */
    protected function isHttpException(Throwable $exception): bool
    {
        return $exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException;
    }


    protected function getLogLevel(Throwable $exception):string
    {
        if ($exception instanceof ModelNotFoundException ||
            $exception instanceof NotFoundHttpException ||
            $exception instanceof ValidationException ||
            $exception instanceof AuthenticationException ||
            $exception instanceof AuthorizationException ||
            $exception instanceof ExceptionsTooManySlugAttemptsException ||
            $exception instanceof MethodNotAllowedHttpException) {
            return 'info';
        }
        
        return 'error';
    }

        protected function renderUniversalErrorView(Request $request, int $statusCode, Throwable $exception, array $customData = [])
    {
        $data = array_merge([
            'statusCode' => $statusCode,
            'title' => $this->getErrorTitle($statusCode),
            'message' => $this->getErrorMessage($statusCode, $exception),
            'errorId' => $this->generateErrorId(),
        ], $customData);

        // Add exception details in debug mode
        if (config('app.debug')) {
            $data['details'] = get_class($exception) . ' in ' . basename($exception->getFile()) . ':' . $exception->getLine();
            $data['trace'] = $exception->getTraceAsString();
        }

        return response()->view('exceptions.index', $data, $statusCode);
    }

     protected function getErrorTitle(int $statusCode): string
    {
        return match($statusCode) {
            400 => 'Permintaan Tidak Valid',
            401 => 'Akses Tidak Diizinkan', 
            403 => 'Akses Ditolak',
            404 => 'Halaman Tidak Ditemukan',
            405 => 'Metode Tidak Diizinkan',
            419 => 'Sesi Telah Berakhir',
            422 => 'Data Tidak Valid',
            429 => 'Terlalu Banyak Permintaan',
            500 => 'Kesalahan Server Internal',
            503 => 'Layanan Tidak Tersedia',
            default => 'Terjadi Kesalahan'
        };
    }

     protected function getErrorMessage(int $statusCode, Throwable $exception): string
    {
        // Use custom message if available
        if (method_exists($exception, 'getMessage') && !empty($exception->getMessage())) {
            // Don't expose sensitive information in production
            if (!config('app.debug') && in_array($statusCode, [500, 503])) {
                return $this->getDefaultErrorMessage($statusCode);
            }
            return $exception->getMessage();
        }

        return $this->getDefaultErrorMessage($statusCode);
    }

     protected function getDefaultErrorMessage(int $statusCode): string
    {
        return match($statusCode) {
            400 => 'Data yang Anda kirim tidak sesuai format yang diharapkan.',
            401 => 'Anda perlu login terlebih dahulu untuk mengakses halaman ini.',
            403 => 'Anda tidak memiliki izin untuk mengakses halaman ini.',
            404 => 'Halaman yang Anda cari tidak dapat ditemukan. Mungkin URL salah atau halaman telah dipindahkan.',
            405 => 'Metode HTTP yang Anda gunakan tidak diizinkan untuk endpoint ini.',
            419 => 'Sesi Anda telah berakhir untuk keamanan. Silakan muat ulang halaman.',
            422 => 'Data yang Anda masukkan tidak valid. Silakan periksa kembali.',
            429 => 'Anda telah mencoba terlalu banyak dalam waktu singkat. Silakan tunggu sebentar.',
            500 => 'Terjadi kesalahan pada server. Tim kami akan segera memperbaikinya.',
            503 => 'Layanan sedang dalam pemeliharaan. Silakan coba lagi nanti.',
            default => 'Terjadi kesalahan yang tidak terduga. Silakan coba lagi nanti.'
        };
    }

    protected function generateErrorId(): string
    {
        return strtoupper(Str::random(8)) . '-' . date('YmdHis');
    }
}
