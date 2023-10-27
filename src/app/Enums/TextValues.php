<?php

namespace App\Enums;

enum TextValues: string {
    case EXPERT_CACHED_INFO = 'expert_cached_info';
    case STUDENT_NAME = 'student_name';
    case CONSULTATION_WITH_EXPERTS = 'consultation_with_experts';
    case CONSULTATION_CACHED_INFO = 'consultation_cached_info';
    case EXPERT_NAME = 'expert_name';
    case EXPERT_BALANCE = 'expert_balance';
    case CONSULTATION_REQUEST_INFO = 'consultation_request_info';
    case SELECT_CONSULTATION_REQUEST_EXPERT = 'select_consultation_request_expert';
    case CONSULTATION_INFO = 'consultation_info';
    case CONSULTATION_REQUEST_EXPERT_INFO = 'consultation_request_expert_info';
}
