<?php

namespace App\Repositories;

use App\DataModels\Entities\AbstractStep;
use Ramsey\Uuid\UuidInterface;

interface Neo4jMenuStorageRepositoryInterface
{
    public function getStepById(UuidInterface $stepId): ?AbstractStep;

    public function getNextStepByAnswerText(UuidInterface $lastStepId, string $answerText): ?AbstractStep;

    public function getBackStepByCurrentStepId(UuidInterface $currentStepId): ?AbstractStep;

}
