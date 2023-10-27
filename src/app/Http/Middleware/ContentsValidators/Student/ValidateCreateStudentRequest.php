<?php

namespace App\Http\Middleware\ContentsValidators\Student;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidateCreateStudentRequest
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
                'first_name' => ['required', 'string', 'max:255'],
                'telegram_client_id' => ['required', 'integer', 'max:255'],
                'contact_phone_number' => ['digits:11'],
            ]
        );

        $validator->validate();
    }
}
