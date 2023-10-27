<?php

namespace App\Exceptions\Service;

use App\Constants\Errors;
use App\Exceptions\ClientResponseCodeInterface;
use App\Exceptions\ClientResponseMessageInterface;

class StudentPhoneNumberCreatedException extends \Exception implements ClientResponseCodeInterface, ClientResponseMessageInterface
{
    public static function create(): StudentPhoneNumberCreatedException
    {
        return new self(
            message: 'Данный \'contact_phone_number\' уже занят другим учеником',
            code: Errors::CODE_STUDENT_PHONE_NUMBER_EXISTS
        );
    }
}
