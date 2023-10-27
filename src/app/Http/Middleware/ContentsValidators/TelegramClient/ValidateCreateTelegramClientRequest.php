<?php

namespace App\Http\Middleware\ContentsValidators\TelegramClient;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidateCreateTelegramClientRequest
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
                'telegram_id' => ['required', 'digits:9'],
                'first_name' => ['string', 'max:255'],
                'last_name' => ['string', 'max:255'],
                'username' => ['string', 'max:255'],
            ]
        );

        $validator->validate();
    }
}
