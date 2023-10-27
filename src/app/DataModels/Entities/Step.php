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

class Step extends AbstractStep
{
    private UuidInterface $id;
    private StepTypes $type;
    private ?Branches $branch;
    private AnswersBagInterface $answersBag;
    private ?string $text;
    private array $valuesInText;
    private array $filesInText;
    private bool $canMessageParameter;
    private bool $isMessageParameterRequired;
    private ?MessageParameters $messageParameter;
    private ?MessageParameterTypes $messageParameterType;
    private ?string $inLineKeyboardText;
    private bool $canInLineKeyboard;
    private bool $canRequestContact;
    private bool $canThereBackAnswer;
    private bool $isInLineKeyboardDelete;
    private ?InLineKeyboardParameters $inLineKeyboardParameter;
    private ?InLineKeyboardParameterTypes $inLineKeyboardParameterType;
    private bool $isInLineKeyboardParameterRequired;

    public function __construct(
        UuidInterface                $id,
        StepTypes                    $type,
        ?Branches                    $branch,
        AnswersBagInterface          $answersBag,
        ?string                      $text,
        array                        $valuesInText,
        array                        $filesInText,
        bool                         $canMessageParameter,
        bool                         $isMessageParameterRequired,
        ?MessageParameters           $messageParameter,
        ?MessageParameterTypes       $messageParameterType,
        bool                         $canInLineKeyboard,
        ?string                      $inLineKeyboardText,
        bool                         $isInLineKeyboardDelete,
        bool                         $isInLineKeyboardParameterRequired,
        ?InLineKeyboardParameters    $inLineKeyboardParameter,
        ?InLineKeyboardParameterTypes $inLineKeyboardParameterType,
        bool                         $canRequestContact,
        bool                         $canThereBackAnswer
    )
    {
        $this->id = $id;
        $this->type = $type;
        $this->branch = $branch;
        $this->answersBag = $answersBag;
        $this->text = $text;
        $this->valuesInText = $valuesInText;
        $this->filesInText = $filesInText;
        $this->canMessageParameter = $canMessageParameter;
        $this->isMessageParameterRequired = $isMessageParameterRequired;
        $this->messageParameter = $messageParameter;
        $this->messageParameterType = $messageParameterType;
        $this->canInLineKeyboard = $canInLineKeyboard;
        $this->inLineKeyboardText = $inLineKeyboardText;
        $this->isInLineKeyboardDelete = $isInLineKeyboardDelete;
        $this->inLineKeyboardParameter = $inLineKeyboardParameter;
        $this->inLineKeyboardParameterType = $inLineKeyboardParameterType;
        $this->isInLineKeyboardParameterRequired = $isInLineKeyboardParameterRequired;
        $this->canRequestContact = $canRequestContact;
        $this->canThereBackAnswer = $canThereBackAnswer;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return StepTypes
     */
    public function getType(): StepTypes
    {
        return $this->type;
    }

    /**
     * @return Branches|null
     */
    public function getBranch(): ?Branches
    {
        return $this->branch;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @return TextValues[]
     */
    public function getValuesInText(): array
    {
        return $this->valuesInText;
    }

    /**
     * @return TextFiles[]
     */
    public function getFilesInText(): array
    {
        return $this->filesInText;
    }

    /**
     * @return MessageParameters|null
     */
    public function getMessageParameter(): ?MessageParameters
    {
        return $this->messageParameter;
    }

    /**
     * @return MessageParameterTypes|null
     */
    public function getMessageParameterType(): ?MessageParameterTypes
    {
        return $this->messageParameterType;
    }

    /**
     * @return bool
     */
    public function isMessageParameterRequired(): bool
    {
        return $this->isMessageParameterRequired;
    }

    /**
     * @return string|null
     */
    public function getInLineKeyboardText(): ?string
    {
        return $this->inLineKeyboardText;
    }

    /**
     * @return bool
     */
    public function canInLineKeyboard(): bool
    {
        return $this->canInLineKeyboard;
    }

    /**
     * @return bool
     */
    public function canRequestContact(): bool
    {
        return $this->canRequestContact;
    }

    /**
     * @return bool
     */
    public function canThereBackAnswer(): bool
    {
        return $this->canThereBackAnswer;
    }

    /**
     * @return AnswersBagInterface
     */
    public function getAnswersBag(): AnswersBagInterface
    {
        return $this->answersBag;
    }

    public function canMessageParameter(): bool
    {
        return $this->canMessageParameter;
    }

    public function isInLineKeyboardParameterRequired(): bool
    {
        return $this->isInLineKeyboardParameterRequired;
    }

    public function getInLineKeyboardParameter(): ?InLineKeyboardParameters
    {
        return $this->inLineKeyboardParameter;
    }

    public function getInLineKeyboardParameterType(): ?InLineKeyboardParameterTypes
    {
        return $this->inLineKeyboardParameterType;
    }

    public function isInLineKeyboardDelete(): bool
    {
        return $this->isInLineKeyboardDelete;
    }
}
