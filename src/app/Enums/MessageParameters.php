<?php

namespace App\Enums;

enum MessageParameters: string {
    case CLIENT_MESSAGE = 'client_message';
    case FULL_NAME = 'full_name';
    case TELEGRAM_PHONE_NUMBER = 'telegram_phone_number';
    case WHATSAPP_PHONE_NUMBER = 'whatsapp_phone_number';
    case BIOGRAPHY = 'biography';
    case CONSULTATION_REQUEST_TEXT = 'consultation_request_text';
}
