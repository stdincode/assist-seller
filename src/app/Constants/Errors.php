<?php

namespace App\Constants;

interface Errors
{
    public const CODE_REQUEST_VALIDATION = -40001;
    public const CODE_EXPERT_TELEGRAM_PHONE_NUMBER_EXISTS = -40003;
    public const CODE_EXPERT_WHATSAPP_PHONE_NUMBER_EXISTS = -40004;
    public const CODE_STUDENT_PHONE_NUMBER_EXISTS = -40007;
    public const CODE_TELEGRAM_CLIENT_NOT_EXISTS = -40008;
    public const CODE_TELEGRAM_CLIENT_ID_EXISTS = -40009;
    public const CODE_PLACE_ID_NOT_EXISTS = -40010;
    public const CODE_SPECIALIZATION_ID_NOT_EXISTS = -40011;
    public const CODE_EXPERT_NOT_EXISTS = -40012;
    public const CODE_EXPERT_BALANCE_AT_ZERO = -40013;
    public const CODE_EXPERT_STATUS_REQUEST_EXISTS = -40014;
    public const CODE_NOT_ENOUGH_EXPERT_BALANCE = -40015;
    public const CODE_STUDENT_NOT_EXISTS = -40016;

    public const CODE_EXPERT_PAYMENT_NOT_EXISTS = -40019;
    public const CODE_EXPERT_PAYMENT_ALREADY_UPDATED = -40017;
    public const CODE_TELEGRAM_ID_EXISTS = -40018;

    public const CODE_INTERNAL_ERROR = -50001;
}