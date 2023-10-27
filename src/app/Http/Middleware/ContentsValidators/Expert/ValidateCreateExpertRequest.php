<?php

namespace App\Http\Middleware\ContentsValidators\Expert;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidateCreateExpertRequest
{
    public function handle(Request $request, Closure $next)
    {
        $params = $request->post();

        $this->validate($params + $request->file());

        return $next($request);
    }

    private function validate(array $params): void
    {
        $validator = Validator::make(
            $params,
            [
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'patronymic' => ['required', 'string', 'max:255'],
                'biography' => ['required', 'string'],
                'telegram_client_id' => ['required', 'integer', 'max:255'],
                'telegram_phone_number' => ['required', 'digits:11'],
                'whatsapp_phone_number' => ['digits:11'],
                'price_work_hour' => ['required', 'numeric', 'between:0.00,99999.99'],
                'requisites' => ['required', 'string'],
                'avatar' => ['mimes:jpeg,bmp,png', 'max:2048', 'nullable'],
                'video' => ['mimes:mp4,mpeg', 'max:20480', 'nullable'],
                'place_ids' => ['array', 'nullable'],
                'place_ids.*' => ['integer'],
                'specialization_ids' => ['array', 'nullable'],
                'specialization_ids.*' => ['integer'],
            ]
        );

        $validator->validate();
    }
}
