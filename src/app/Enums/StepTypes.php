<?php

namespace App\Enums;

enum StepTypes: string {
    case INITIAL_STEP = 'initial_step';
    case STEP = 'step';
    case FINAL_STEP = 'final_step';
}
