<?php

namespace App\Exceptions\Service;

use App\Constants\Errors;
use App\Exceptions\ClientResponseCodeInterface;
use App\Exceptions\ClientResponseMessageInterface;

class TelegramClientNotExistsException extends \Exception implements ClientResponseCodeInterface, ClientResponseMessageInterface
{
    public static function create(): TelegramClientNotExistsException
    {
        return new self(
            message: 'Данный пользователь телеграма с \'telegram_client_id\' в нашей системе не зарегистрирован',
            code: Errors::CODE_TELEGRAM_CLIENT_NOT_EXISTS
        );
    }
}
