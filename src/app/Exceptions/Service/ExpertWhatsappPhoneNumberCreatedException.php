<?php

namespace App\Exceptions\Service;

use App\Constants\Errors;
use App\Exceptions\ClientResponseCodeInterface;
use App\Exceptions\ClientResponseMessageInterface;

class ExpertWhatsappPhoneNumberCreatedException extends \Exception implements ClientResponseCodeInterface, ClientResponseMessageInterface
{
    public static function create(): ExpertWhatsappPhoneNumberCreatedException
    {
        return new self(
            message: 'Данный \'whatsapp_phone_number\' уже занят другим экспертом',
            code: Errors::CODE_EXPERT_WHATSAPP_PHONE_NUMBER_EXISTS
        );
    }
}
