<?php

namespace App\Exceptions;

use App\Constants\Errors;

class InvalidResourceIdMessageInterface extends \Exception implements
    ClientResponseMessageInterface,
    ClientResponseCodeInterface
{
    protected $code = Errors::CODE_REQUEST_VALIDATION;
}
