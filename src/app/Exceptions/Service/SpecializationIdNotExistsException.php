<?php

namespace App\Exceptions\Service;

use App\Constants\Errors;
use App\Exceptions\ClientResponseCodeInterface;
use App\Exceptions\ClientResponseMessageInterface;

class SpecializationIdNotExistsException extends \Exception implements ClientResponseCodeInterface, ClientResponseMessageInterface
{
    public static function create(int $specializationId): SpecializationIdNotExistsException
    {
        return new self(
            message: "Данный идентификатор специализации: {$specializationId} в нашей системе не существует",
            code: Errors::CODE_SPECIALIZATION_ID_NOT_EXISTS
        );
    }
}
