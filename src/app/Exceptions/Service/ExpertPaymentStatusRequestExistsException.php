<?php

namespace App\Exceptions\Service;

use App\Constants\Errors;
use App\Exceptions\ClientResponseCodeInterface;
use App\Exceptions\ClientResponseMessageInterface;

class ExpertPaymentStatusRequestExistsException extends \Exception implements ClientResponseCodeInterface, ClientResponseMessageInterface
{
    public static function create(): ExpertPaymentStatusRequestExistsException
    {
        return new self(
            message: 'Запрос на выплату уже существует',
            code: Errors::CODE_EXPERT_STATUS_REQUEST_EXISTS
        );
    }
}
