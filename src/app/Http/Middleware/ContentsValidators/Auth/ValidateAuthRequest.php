<?php

namespace App\Http\Middleware\ContentsValidators\Auth;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;


class ValidateAuthRequest
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
                'login' => ['required', 'string', 'max:255'],
                'password' => ['required', 'string', Password::min(8)],
            ]
        );

        $validator->validate();
    }
}
