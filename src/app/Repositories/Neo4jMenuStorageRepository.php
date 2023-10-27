<?php

namespace App\Repositories;

use App\DataModels\Entities\Answer;
use App\DataModels\Entities\Bags\AnswersBag;
use App\DataModels\Entities\Bags\AnswersBagInterface;
use App\DataModels\Entities\Step;
use App\DataModels\Entities\AbstractStep;
use App\Enums\Branches;
use App\Enums\InLineKeyboardParameters;
use App\Enums\InLineKeyboardParameterTypes;
use App\Enums\MessageParameters;
use App\Enums\MessageParameterTypes;
use App\Enums\StepTypes;
use App\Enums\TextFiles;
use App\Enums\TextValues;
use Laudis\Neo4j\Contracts\ClientInterface;
use Laudis\Neo4j\Types\CypherList;
use Laudis\Neo4j\Types\CypherMap;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;


class Neo4jMenuStorageRepository implements Neo4jMenuStorageRepositoryInterface
{
    private ClientInterface $neo4jClient;

    public function __construct(
        ClientInterface $neo4jClient
    )
    {
        $this->neo4jClient = $neo4jClient;
    }

    public function getStepById(UuidInterface $stepId): ?AbstractStep
    {
        $step = $this->neo4jClient->run(
            sprintf('
                MATCH  r = (target:Step {id: "%s"})
                RETURN
                target.id as id,
                target.type as type,
                target.branch as branch,
                target.text as text,
                target.values_in_text as values_in_text,
                target.files_in_text as files_in_text,
                target.can_message_parameter as can_message_parameter,
                target.is_message_parameter_required as is_message_parameter_required,
                target.message_parameter as message_parameter,
                target.message_parameter_type as message_parameter_type,
                target.can_in_line_keyboard as can_in_line_keyboard,
                target.in_line_keyboard_text as in_line_keyboard_text,
                target.is_in_line_keyboard_delete as is_in_line_keyboard_delete,
                target.in_line_keyboard_parameter as in_line_keyboard_parameter,
                target.in_line_keyboard_parameter_type as in_line_keyboard_parameter_type,
                target.is_in_line_keyboard_parameter_required as is_in_line_keyboard_parameter_required,
                target.can_request_contact as can_request_contact,
                target.can_there_back_answer as can_there_back_answer',
                $stepId->toString()
            )
        )
        ->getResults()
        ->first();

        if (!$step) return null;

        return $this->getStepDataModel($step);
    }

    public function getBackStepByCurrentStepId(UuidInterface $currentStepId): ?AbstractStep
    {
        $step = $this->neo4jClient->run(
            sprintf('
                    MATCH (target:Step)-[answer]->(current:Step {id: "%s"})
                    RETURN
                    target.id as id,
                    target.type as type,
                    target.branch as branch,
                    target.text as text,
                    target.values_in_text as values_in_text,
                    target.files_in_text as files_in_text,
                    target.can_message_parameter as can_message_parameter,
                    target.is_message_parameter_required as is_message_parameter_required,
                    target.message_parameter as message_parameter,
                    target.message_parameter_type as message_parameter_type,
                    target.can_in_line_keyboard as can_in_line_keyboard,
                    target.in_line_keyboard_text as in_line_keyboard_text,
                    target.is_in_line_keyboard_delete as is_in_line_keyboard_delete,
                    target.in_line_keyboard_parameter as in_line_keyboard_parameter,
                    target.in_line_keyboard_parameter_type as in_line_keyboard_parameter_type,
                    target.is_in_line_keyboard_parameter_required as is_in_line_keyboard_parameter_required,
                    target.can_request_contact as can_request_contact,
                    target.can_there_back_answer as can_there_back_answer',
                $currentStepId->toString()
            )
        )
            ->getResults()
            ->first();

        if (!$step) return null;

        return $this->getStepDataModel($step);
    }

    public function getNextStepByAnswerText(UuidInterface $lastStepId, string $answerText): ?AbstractStep
    {
        /**
         * @var CypherList $step
         */
        $step = $this->neo4jClient->run(
            sprintf('
                    MATCH (start_step:Step {id: "%s"})-[current_answer:Answer {text: "%s"}]-(target)
                    RETURN
                    target.id as id,
                    target.type as type,
                    target.branch as branch,
                    target.text as text,
                    target.values_in_text as values_in_text,
                    target.files_in_text as files_in_text,
                    target.can_message_parameter as can_message_parameter,
                    target.is_message_parameter_required as is_message_parameter_required,
                    target.message_parameter as message_parameter,
                    target.message_parameter_type as message_parameter_type,
                    target.can_in_line_keyboard as can_in_line_keyboard,
                    target.in_line_keyboard_text as in_line_keyboard_text,
                    target.is_in_line_keyboard_delete as is_in_line_keyboard_delete,
                    target.in_line_keyboard_parameter as in_line_keyboard_parameter,
                    target.in_line_keyboard_parameter_type as in_line_keyboard_parameter_type,
                    target.is_in_line_keyboard_parameter_required as is_in_line_keyboard_parameter_required,
                    target.can_request_contact as can_request_contact,
                    target.can_there_back_answer as can_there_back_answer',
                $lastStepId->toString(),
                $answerText
            )
        )
            ->getResults();

        if ($step->count() === 0) return null;

        return $this->getStepDataModel($step->first());
    }
    private function getStepTypeEnum(string $stepType): ?StepTypes
    {
        return match ($stepType) {
            StepTypes::INITIAL_STEP->value => StepTypes::INITIAL_STEP,
            StepTypes::STEP->value => StepTypes::STEP,
            StepTypes::FINAL_STEP->value => StepTypes::FINAL_STEP
        };
    }

    private function getStepDataModel(CypherMap $step): AbstractStep
    {
        $answersBag = $this->getAnswersBagByStepId($step->get('id'));

        $valuesInText = [];
        if ($step->get('values_in_text')) {
            $values = explode(',', $step->get('values_in_text'));
            foreach ($values as $value) {
                $valuesInText[] = TextValues::tryFrom($value);
            }
        }

        $filesInText = [];
        if ($step->get('files_in_text')) {
            $values = explode(',', $step->get('files_in_text'));
            foreach ($values as $value) {
                $filesInText[] = TextFiles::tryFrom($value);
            }
        }

        return new Step(
            id: Uuid::fromString($step->get('id')),
            type: $this->getStepTypeEnum($step->get('type')),
            branch: Branches::tryFrom($step->get('branch')),
            answersBag: $answersBag,
            text: $step->get('text'),
            valuesInText: $valuesInText,
            filesInText: $filesInText,
            canMessageParameter: $step->get('can_message_parameter'),
            isMessageParameterRequired: $step->get('is_message_parameter_required'),
            messageParameter: MessageParameters::tryFrom($step->get('message_parameter')),
            messageParameterType: MessageParameterTypes::tryFrom($step->get('message_parameter_type')),
            canInLineKeyboard: $step->get('can_in_line_keyboard'),
            inLineKeyboardText: $step->get('in_line_keyboard_text'),
            isInLineKeyboardDelete: $step->get('is_in_line_keyboard_delete'),
            isInLineKeyboardParameterRequired: $step->get('is_in_line_keyboard_parameter_required'),
            inLineKeyboardParameter: InLineKeyboardParameters::tryFrom($step->get('in_line_keyboard_parameter')),
            inLineKeyboardParameterType: InLineKeyboardParameterTypes::tryFrom($step->get('in_line_keyboard_parameter_type')),
            canRequestContact: $step->get('can_request_contact'),
            canThereBackAnswer: $step->get('can_there_back_answer')
        );
    }

    private function getAnswersBagByStepId(string $stepId): AnswersBagInterface
    {
        $answers = $this->neo4jClient->run(
            sprintf('
                MATCH (:Step {id: "%s"})-[answer]->(target)
                RETURN
                answer.id as answer_id,
                answer.text as answer_text',
                $stepId
            )
        )->getResults();

        $answersBag = new AnswersBag();

        foreach ($answers as $answer) {
            $answersBag->add(
                new Answer(
                    id: Uuid::fromString($answer->get('answer_id')),
                    text: $answer->get('answer_text')
                )
            );
        }

        return $answersBag;
    }

}
