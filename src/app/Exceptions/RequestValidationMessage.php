<?php

namespace App\Exceptions;

class RequestValidationMessage extends \Exception implements
    ClientResponseMessageInterface,
    ClientResponseCodeInterface
{
}
