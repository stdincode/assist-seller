<?php

namespace App\Exceptions\Service;

use App\Constants\Errors;
use App\Exceptions\ClientResponseCodeInterface;
use App\Exceptions\ClientResponseMessageInterface;

class StudentNotExistsException extends \Exception implements ClientResponseCodeInterface, ClientResponseMessageInterface
{
    public static function create(): StudentNotExistsException
    {
        return new self(
            message: 'Данный ученик не существует',
            code: Errors::CODE_STUDENT_NOT_EXISTS
        );
    }
}
