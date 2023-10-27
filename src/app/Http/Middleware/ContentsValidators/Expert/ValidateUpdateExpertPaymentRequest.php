<?php

namespace App\Http\Middleware\ContentsValidators\Expert;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ValidateUpdateExpertPaymentRequest
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
                'status_id' => ['integer', Rule::in(1, 2, 3)],
            ]
        );

        $validator->validate();
    }
}
