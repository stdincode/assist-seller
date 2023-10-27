<?php

namespace App\Http\Middleware\ContentsValidators\Expert;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidateUpdateExpertRequest
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
                'first_name' => ['string', 'max:255'],
                'last_name' => ['string', 'max:255'],
                'patronymic' => ['string', 'max:255'],
                'biography' => ['string'],
                'telegram_phone_number' => ['digits:11'],
                'whatsapp_phone_number' => ['digits:11'],
                'price_work_hour' => ['numeric', 'between:0.00,99999.99'],
                'requisites' => ['string'],
                'is_verification' => ['boolean'],
                'is_blocked' => ['boolean'],
                'avatar' => ['mimes:jpeg,bmp,png', 'max:2048'],
                'video' => ['mimes:mp4,mpeg', 'max:20480'],
                'place_ids' => ['array'],
                'place_ids.*' => ['integer'],
                'specialization_ids' => ['array'],
                'specialization_ids.*' => ['integer'],
            ]
        );

        $validator->validate();
    }
}
