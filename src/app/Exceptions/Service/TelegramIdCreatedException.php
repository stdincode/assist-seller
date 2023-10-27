<?php

namespace App\Exceptions\Service;

use App\Constants\Errors;
use App\Exceptions\ClientResponseCodeInterface;
use App\Exceptions\ClientResponseMessageInterface;

class TelegramIdCreatedException extends \Exception implements ClientResponseCodeInterface, ClientResponseMessageInterface
{
    public static function create(): TelegramIdCreatedException
    {
        return new self(
            message: 'Данный \'telegram_id\' уже занят другим пользователем',
            code: Errors::CODE_TELEGRAM_ID_EXISTS
        );
    }
}
