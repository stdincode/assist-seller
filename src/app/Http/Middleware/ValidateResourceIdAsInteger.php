<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Валидирует идентификатор клиента и помещает
 * его в конфиг для последующего использования
 */
class ValidateResourceIdAsInteger
{
    public function handle(Request $request, Closure $next)
    {
        $id = $request->route('id');
        $this->validate('id', $id);

        return $next($request);
    }

    private function validate(string $name, ?string $id): void
    {
        $validator = Validator::make(
            [
                $name => $id,
            ],
            [
                $name => ['required', 'integer'],
            ]
        );

        $validator->validate();
    }
}
