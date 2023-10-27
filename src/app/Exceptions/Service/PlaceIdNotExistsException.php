<?php

namespace App\Exceptions\Service;

use App\Constants\Errors;
use App\Exceptions\ClientResponseCodeInterface;
use App\Exceptions\ClientResponseMessageInterface;

class PlaceIdNotExistsException extends \Exception implements ClientResponseCodeInterface, ClientResponseMessageInterface
{
    public static function create(int $placeId): PlaceIdNotExistsException
    {
        return new self(
            message: "Данный идентификатор площадки: {$placeId} в нашей системе не существует",
            code: Errors::CODE_PLACE_ID_NOT_EXISTS
        );
    }
}
