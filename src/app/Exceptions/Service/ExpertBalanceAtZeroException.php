<?php

namespace App\Exceptions\Service;

use App\Constants\Errors;
use App\Exceptions\ClientResponseCodeInterface;
use App\Exceptions\ClientResponseMessageInterface;

class ExpertBalanceAtZeroException extends \Exception implements ClientResponseCodeInterface, ClientResponseMessageInterface
{
    public static function create(): ExpertBalanceAtZeroException
    {
        return new self(
            message: 'Баланс эксперта не позволяет сделать выплату',
            code: Errors::CODE_EXPERT_BALANCE_AT_ZERO
        );
    }
}
