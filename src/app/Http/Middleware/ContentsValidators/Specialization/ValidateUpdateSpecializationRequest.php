<?php

namespace App\Http\Middleware\ContentsValidators\Specialization;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidateUpdateSpecializationRequest
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
                'name' => ['required', 'string', 'max:255'],
            ]
        );

        $validator->validate();
    }
}
