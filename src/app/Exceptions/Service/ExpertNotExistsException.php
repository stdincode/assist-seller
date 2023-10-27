<?php

namespace App\Exceptions\Service;

use App\Constants\Errors;
use App\Exceptions\ClientResponseCodeInterface;
use App\Exceptions\ClientResponseMessageInterface;

class ExpertNotExistsException extends \Exception implements ClientResponseCodeInterface, ClientResponseMessageInterface
{
    public static function create(): ExpertNotExistsException
    {
        return new self(
            message: 'Данный эксперт не существует',
            code: Errors::CODE_EXPERT_NOT_EXISTS
        );
    }
}
