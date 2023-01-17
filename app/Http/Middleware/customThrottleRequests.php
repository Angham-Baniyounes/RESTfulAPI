<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;

class customThrottleRequests extends ThrottleRequests
{
    use ApiResponser;

    protected function buildException($request, $key, $maxAttempts, $responseCallback = null)
    {
        $response = $this->errorResponse('Too Many Attempts.', 429);

        $retryAfter = $this->limiter->availableIn($key);

        return $this->addHeaders(
            $response, $maxAttempts,
            $this->calculateRemainingAttempts($key, $maxAttempts, $retryAfter),
            $retryAfter
        );
    }
}
