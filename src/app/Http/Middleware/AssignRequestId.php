<?php

namespace App\Http\Middleware;

use App\Constants\ClientApi;
use Closure;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class AssignRequestId
{
    public function handle(Request $request, Closure $next)
    {
        config([ClientApi::REQUEST_ID => Uuid::uuid4()->toString()]);

        return $next($request);
    }
}
