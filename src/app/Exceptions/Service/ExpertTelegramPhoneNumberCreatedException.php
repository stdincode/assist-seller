<?php

namespace App\Exceptions\Service;

use App\Constants\Errors;
use App\Exceptions\ClientResponseCodeInterface;
use App\Exceptions\ClientResponseMessageInterface;

class ExpertTelegramPhoneNumberCreatedException extends \Exception implements ClientResponseCodeInterface, ClientResponseMessageInterface
{
    public static function create(): ExpertTelegramPhoneNumberCreatedException
    {
        return new self(
            message: 'Данный \'telegram_phone_number\' уже занят другим экспертом',
            code: Errors::CODE_EXPERT_TELEGRAM_PHONE_NUMBER_EXISTS
        );
    }
}
