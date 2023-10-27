<?php

namespace App\Enums;

enum InLineKeyboardParameters: string {
    case PLACES = 'places';
    case SPECIALIZATIONS = 'specializations';
    case CONSULTATION_DATE = 'consultation_date';
    case CONSULTATION_TIME = 'consultation_time';
    case STUDENT_CONSULTATION_REQUESTS = 'student_consultation_requests';
    case STUDENT_CONSULTATIONS = 'student_consultations';
    case EXPERT_CONSULTATION_REQUESTS = 'expert_consultation_requests';
    case EXPERT_CONSULTATIONS = 'expert_consultations';


}
