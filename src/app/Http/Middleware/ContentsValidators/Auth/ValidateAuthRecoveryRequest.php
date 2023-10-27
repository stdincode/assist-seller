<?php

namespace App\Http\Middleware\ContentsValidators\Auth;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ValidateAuthRecoveryRequest
{
    public function handle(Request $request, Closure $next)
    {
        $params = $request->post();
        $this->validate($params);

        return $next($request);
    }

    private function validate(array $params): void
    {
        $validator = Validator::make(
            $params,
            [
                'email' => ['required', 'email', 'max:255'],
            ]
        );

        $validator->validate();
    }
}
