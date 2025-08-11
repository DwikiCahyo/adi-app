<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class TooManySlugAttemptsException extends Exception

{

    protected $message = "Too many attempts Request";
    protected $code = Response::HTTP_TOO_MANY_REQUESTS;

    public function __construct($message=null,$code=0){
        parent::__construct(
            $message ?? $this -> message,
            $code ?: $this -> code,
        );
    }

    public function context():array{
        return [
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ];
    }

    public function report():void{

        $logInfo = [
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
            'url' => request()->fullUrl(),
        ];

        Log::warning("Rate limit exceeded for news slug lookup",$logInfo );
    }
}
