<?php

namespace App\Enums;

enum Branches: string {
    case COMMON = 'common';
    case COMMON_EXPERT = 'common_expert';
    case COMMON_STUDENT = 'common_student';
    case EXPERT = 'expert';
    case EXPERT_CONSULTATIONS = 'expert_consultations';
    case EXPERT_CONSULTATION_CANCEL = 'expert_consultation_cancel';
    case EXPERT_BALANCE = 'expert_balance';
    case STUDENT = 'student';
    case STUDENT_CONSULTATIONS = 'student_consultations';
    case STUDENT_CONSULTATION_CANCEL = 'student_consultation_cancel';
    case STUDENT_CONSULTATION_SELECT_EXPERT = 'student_consultation_select_expert';
    case STUDENT_SUPPORT = 'student_support';
    case STUDENT_CONSULTATION_CREATE = 'student_consultation_create';
    case STUDENT_CONSULTATION_REQUESTS = 'student_consultation_requests';
    case STUDENT_CONSULTATION_REQUEST_CANCEL = 'student_consultation_request_cancel';
    case STUDENT_CONSULTATION_REQUEST_SELECT_EXPERT = 'student_consultation_request_select_expert';
    case CONSULTATION_REQUEST_EXPERT = 'consultation_request_expert';
    case CONSULTATION_REQUEST_EXPERT_ACCEPT = 'consultation_request_expert_accept';
    case CONSULTATION_REQUEST_EXPERT_CONTINUE = 'consultation_request_expert_continue';
}
