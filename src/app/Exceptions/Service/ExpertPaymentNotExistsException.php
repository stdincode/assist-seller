<?php

namespace App\Exceptions\Service;

use App\Constants\Errors;
use App\Exceptions\ClientResponseCodeInterface;
use App\Exceptions\ClientResponseMessageInterface;

class ExpertPaymentNotExistsException extends \Exception implements ClientResponseCodeInterface, ClientResponseMessageInterface
{
    public static function create(): ExpertPaymentNotExistsException
    {
        return new self(
            message: 'Данная выплата эксперту не существует',
            code: Errors::CODE_EXPERT_PAYMENT_NOT_EXISTS
        );
    }
}
