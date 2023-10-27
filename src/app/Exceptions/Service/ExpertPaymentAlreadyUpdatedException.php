<?php

namespace App\Exceptions\Service;

use App\Constants\Errors;
use App\Exceptions\ClientResponseCodeInterface;
use App\Exceptions\ClientResponseMessageInterface;

class ExpertPaymentAlreadyUpdatedException extends \Exception implements ClientResponseCodeInterface, ClientResponseMessageInterface
{
    public static function create(): ExpertPaymentAlreadyUpdatedException
    {
        return new self(
            message: 'Данная выплата эксперту уже была изменена ранее',
            code: Errors::CODE_EXPERT_PAYMENT_ALREADY_UPDATED
        );
    }
}
