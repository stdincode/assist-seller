<?php

namespace App\Http\Middleware\ParamsValidators;

use App\Enums\SortParams\SortDirections;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class ValidatePageParamsRequest
{
    public function handle(Request $request, Closure $next)
    {
        $params = $request->input();

        $this->validate($params);

        return $next($request);
    }

    private function validate(array $params): void
    {
        $sortDirectionList = [];
        foreach (SortDirections::cases() as $param) {
            $sortDirectionList[] = $param->value;
        }

        $validator = Validator::make(
            $params,
            [
                'sort_direction' => [Rule::in($sortDirectionList),],
                'page' => ['integer'],
                'per_page' => ['integer'],
            ]
        );

        $validator->validate();
    }
}
