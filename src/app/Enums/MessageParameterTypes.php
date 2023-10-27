<?php

namespace App\Enums;

enum MessageParameterTypes: string {
    case TEXT = 'text';
    case FULL_NAME = 'full_name';
    case PHONE_NUMBER = 'phone_number';
}
