<?php

namespace App\Exceptions\Service;

use App\Constants\Errors;
use App\Exceptions\ClientResponseCodeInterface;
use App\Exceptions\ClientResponseMessageInterface;

class TelegramClientIdCreatedException extends \Exception implements ClientResponseCodeInterface, ClientResponseMessageInterface
{
    public static function create(): TelegramClientIdCreatedException
    {
        return new self(
            message: 'Данный \'telegram_client_id\' уже занят другим пользователем',
            code: Errors::CODE_TELEGRAM_CLIENT_ID_EXISTS
        );
    }
}
