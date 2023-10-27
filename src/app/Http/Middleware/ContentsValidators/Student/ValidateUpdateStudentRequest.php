<?php

namespace App\Http\Middleware\ContentsValidators\Student;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidateUpdateStudentRequest
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
                'first_name' => ['string', 'max:255'],
                'contact_phone_number' => ['digits:11'],
                'is_blocked' => ['boolean'],
            ]
        );

        $validator->validate();
    }
}
