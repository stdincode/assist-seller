<?php

namespace App\DataModels\Entities;

use App\DataModels\Entities\Bags\AnswersBagInterface;
use App\Enums\Branches;
use App\Enums\InLineKeyboardParameters;
use App\Enums\InLineKeyboardParameterTypes;
use App\Enums\MessageParameters;
use App\Enums\MessageParameterTypes;
use App\Enums\StepTypes;
use App\Enums\TextFiles;
use App\Enums\TextValues;
use Ramsey\Uuid\UuidInterface;

abstract class AbstractStep implements EntityInterface
{
    /**
     * @return UuidInterface
     */
    abstract public function getId(): UuidInterface;

    /**
     * @return StepTypes
     */
    abstract public function getType(): StepTypes;

    /**
     * @return Branches|null
     */
    abstract public function getBranch(): ?Branches;

    /**
     * @return string|null
     */
    abstract public function getText(): ?string;

    /**
     * @return TextValues[]
     */
    abstract public function getValuesInText(): array;

    /**
     * @return TextFiles[]
     */
    abstract public function getFilesInText(): array;

    /**
     * @return bool
     */
    abstract public function canMessageParameter(): bool;

    /**
     * @return bool
     */
    abstract public function isMessageParameterRequired(): bool;

    /**
     * @return MessageParameters|null
     */
    abstract public function getMessageParameter(): ?MessageParameters;

    /**
     * @return MessageParameterTypes|null
     */
    abstract public function getMessageParameterType(): ?MessageParameterTypes;

    /**
     * @return bool
     */
    abstract public function canInLineKeyboard(): bool;

    /**
     * @return bool
     */
    abstract public function isInLineKeyboardParameterRequired(): bool;

    /**
     * @return string|null
     */
    abstract public function getInLineKeyboardText(): ?string;

    /**
     * @return InLineKeyboardParameters|null
     */
    abstract public function getInLineKeyboardParameter(): ?InLineKeyboardParameters;

    /**
     * @return InLineKeyboardParameterTypes|null
     */
    abstract public function getInLineKeyboardParameterType(): ?InLineKeyboardParameterTypes;

    /**
     * @return bool
     */
    abstract public function isInLineKeyboardDelete(): bool;

    /**
     * @return bool
     */
    abstract public function canRequestContact(): bool;

    /**
     * @return bool
     */
    abstract public function canThereBackAnswer(): bool;

    /**
     * @return AnswersBagInterface
     */
    abstract public function getAnswersBag(): AnswersBagInterface;

    public function asArray(): array
    {
        return [
            'id' => $this->getId()->toString(),
            'type' => $this->getType()->value,
            'branch' => $this->getBranch()->value,
            'text' => $this->getText(),
            'values_in_text' => $this->getValuesInText(),
            'files_in_text' => $this->getFilesInText(),
            'can_message_parameter' => $this->canMessageParameter(),
            'is_message_parameter_required' => $this->isMessageParameterRequired(),
            'message_parameter' => $this->getMessageParameter()->value,
            'message_parameter_type' => $this->getMessageParameterType()->value,
            'can_in_line_keyboard' => $this->canInLineKeyboard(),
            'in_line_keyboard_text' => $this->getInLineKeyboardText(),
            'is_in_line_keyboard_delete' => $this->isInLineKeyboardDelete(),
            'in_line_keyboard_parameter' => $this->getInLineKeyboardParameter(),
            'in_line_keyboard_parameter_type' => $this->getInLineKeyboardParameterType(),
            'is_in_line_keyboard_parameter_required' => $this->isInLineKeyboardParameterRequired(),
            'can_request_contact' => $this->canRequestContact(),
            'can_there_back_answer' => $this->canThereBackAnswer(),
            'answers' => $this->getAnswersBag()->asArray(),
        ];
    }
}
