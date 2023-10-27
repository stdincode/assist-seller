<?php

namespace App\Exceptions\Service;

use App\Constants\Errors;
use App\Exceptions\ClientResponseCodeInterface;
use App\Exceptions\ClientResponseMessageInterface;

class NotEnoughExpertBalanceException extends \Exception implements ClientResponseCodeInterface, ClientResponseMessageInterface
{
    public static function create(): NotEnoughExpertBalanceException
    {
        return new self(
            message: 'Недостаточно средств на балансе эксперта',
            code: Errors::CODE_NOT_ENOUGH_EXPERT_BALANCE
        );
    }
}
