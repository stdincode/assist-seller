<?php

namespace App\Strategies;

use App\DataModels\Entities\AbstractConsultation;
use App\DataModels\Entities\AbstractExpert;
use App\DataModels\Entities\AbstractStep;
use App\DataModels\Entities\AbstractTelegramMenuSession;
use App\DataModels\Entities\Bags\ConsultationsBag;
use App\Enums\Branches;
use App\Enums\InLineKeyboardParameters;
use App\Enums\InLineKeyboardParameterTypes;
use App\Enums\MessageParameters;
use App\Enums\StepTypes;
use App\Enums\TextValues;
use App\Repositories\ConsultationStorageRepository;
use App\Repositories\ConsultationStorageRepositoryInterface;
use App\Repositories\ExpertStorageRepository;
use App\Repositories\ExpertStorageRepositoryInterface;
use App\Repositories\Neo4jMenuStorageRepositoryInterface;
use App\Repositories\TelegramStorageRepository;
use App\Repositories\TelegramStorageRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Message;

class ExpertTelegramMenu extends AbstractTelegramMenu
{
    private Neo4jMenuStorageRepositoryInterface $neo4jMenuStorageRepository;
    private ExpertStorageRepositoryInterface $expertStorageRepository;
    private ConsultationStorageRepositoryInterface $consultationStorageRepository;
    private AbstractExpert $expert;

    public function __construct(
        AbstractExpert $expert,
        Api $apiClient,
        Neo4jMenuStorageRepositoryInterface $neo4jMenuStorageRepository,
        TelegramStorageRepositoryInterface $telegramStorageRepository,
        ExpertStorageRepositoryInterface $expertStorageRepository,
        ConsultationStorageRepositoryInterface $consultationStorageRepository
    )
    {
        $this->expert = $expert;
        $this->neo4jMenuStorageRepository = $neo4jMenuStorageRepository;
        $this->expertStorageRepository = $expertStorageRepository;
        $this->consultationStorageRepository = $consultationStorageRepository;

        parent::__construct(
            apiClient: $apiClient,
            telegramStorageRepository: $telegramStorageRepository
        );
    }

        //это шаг назад?
        //это финальный шаг?
        //это обработка текущего шага?
        //это переход на следующий шаг?

        //шаг требует сохранения в бд закешированных данных?
        //предыдущий шаг был с callback кнопками? удалить их
        //можем достать следующий шаг?
        //есть закешированные данные?

    public function messageHandler(
        Message $message,
        AbstractStep $currentStep,
        AbstractTelegramMenuSession $session
    ): void
    {
        $cachedSessionData = Cache::get($session->getId()) ?? [];

        $nextStep = $this->neo4jMenuStorageRepository->getNextStepByAnswerText(
            lastStepId: $currentStep->getId(),
            answerText: $message->text
        ) ?? null;

        if ($message->text) {
            if (!$nextStep) {
                if (
                    $currentStep->canThereBackAnswer() &&
                    $message->text === TelegramStorageRepository::TEXT_ANSWER_IS_BACK
                ) {
                    $nextStep = $this->neo4jMenuStorageRepository->getBackStepByCurrentStepId($currentStep->getId());
                } elseif (
                    $currentStep->canMessageParameter() &&
                    $currentStep->getMessageParameter() === MessageParameters::CLIENT_MESSAGE
                ) {
                    $this->telegramStorageRepository->saveTelegramClientStepMessage(
                        clientStepId: $message->chat->id,
                        messageId: $message->messageId,
                        message: $message->text
                    );

                    return;
                } else {
                    $answers = $currentStep->getAnswersBag()->getAll();
                    $firstNextAnswer = reset($answers);
                    $nextStep = $this->neo4jMenuStorageRepository->getNextStepByAnswerText(
                        lastStepId: $currentStep->getId(),
                        answerText: $firstNextAnswer->getText()
                    );
                }
            }

            $this->handleNextStep(
                currentStep: $currentStep,
                nextStep: $nextStep,
                session: $session,
                cachedSessionData: $cachedSessionData,
                message: $message
            );
        }

    }

    public function callbackMessageHandler(
        CallbackQuery $callbackQuery,
        AbstractStep $step,
        AbstractTelegramMenuSession $session
    ): void
    {
        $cachedSessionData = Cache::get($session->getId()) ?? [];

        if ($step->getInLineKeyboardParameter() === InLineKeyboardParameters::EXPERT_CONSULTATIONS) {
            $this->cacheInLineKeyboardParameter(
                step: $step,
                session: $session,
                callbackQuery: $callbackQuery,
                cachedSessionData: $cachedSessionData
            );

            $this->attemptDeleteLastCallbackMessage(
                step: $step,
                message: $callbackQuery->message
            );

            $inLineMessageBody = $this->buildInLineMessageBody(
                chatId: $callbackQuery->message->chat->id,
                step: $step,
                cachedSessionData: $cachedSessionData
            );

            $this->apiClient->sendMessage($inLineMessageBody);
        }
    }

    private function cacheInLineKeyboardParameter(
        AbstractStep $step,
        AbstractTelegramMenuSession $session,
        CallbackQuery $callbackQuery,
        array $cachedSessionData
    ): void
    {
        $cachedEntities = $cachedSessionData[$step->getInLineKeyboardParameter()->value] ?? [];
        $inputEntity = json_decode($callbackQuery->data, true, 512, JSON_THROW_ON_ERROR);

        $entity = match ($step->getInLineKeyboardParameter()) {
            InLineKeyboardParameters::EXPERT_CONSULTATIONS => $this->consultationStorageRepository->getConsultationById(id: $inputEntity['id']),
            default => null,
        };

        if ($step->getInLineKeyboardParameterType() === InLineKeyboardParameterTypes::CHECKBOX) {
            if (key_exists($inputEntity['id'], $cachedEntities)) {
                unset($cachedEntities[$inputEntity['id']]);
            } else {
                $cachedEntities[$inputEntity['id']] = $entity;
            }
            $cachedSessionData[$step->getInLineKeyboardParameter()->value] = $cachedEntities;
        } elseif ($step->getInLineKeyboardParameterType() === InLineKeyboardParameterTypes::RADIO) {
            $cachedSessionData[$step->getInLineKeyboardParameter()->value] = [$inputEntity['id'] => $entity];
        }

        $hours = config('telegram.telegram_menu_session_life_time');
        $sessionClosedDateTime = $session->getCreatedAt()->modify("+{$hours} hours");
        Cache::put($session->getId(), $cachedSessionData, $sessionClosedDateTime);
    }

    private function handleNextStep(
        AbstractStep $currentStep,
        AbstractStep $nextStep,
        AbstractTelegramMenuSession $session,
        array $cachedSessionData,
        Message $message
    ): void
    {
        if (
            !$this->isStepParametersCached(step: $currentStep, cachedSessionData: $cachedSessionData) &&
            !$currentStep->canThereBackAnswer() &&
            $message->text !== TelegramStorageRepository::TEXT_ANSWER_IS_BACK
        ) {
            exit;
        }

        if ($nextStep->getType() === StepTypes::FINAL_STEP) {
            $this->handleStepWithAction(message: $message, step: $nextStep, session: $session);
            $this->telegramStorageRepository->closeTelegramSession($session->getId());
        }

        $this->attemptDeleteLastCallbackMessage(
            step: $currentStep,
            message: $message
        );

        $messageBody = $this->buildMessageBody(
            chatId: $message->chat->id,
            step: $nextStep,
            cachedSessionData: $cachedSessionData
        );

        if ($nextStep->canInLineKeyboard()) {
            $inLineMessageBody = $this->buildInLineMessageBody(
                chatId: $message->chat->id,
                step: $nextStep,
                cachedSessionData: $cachedSessionData
            );
        }

        $this->sendStep(
            chatId: $message->chat->id,
            messageId: $message->messageId,
            sessionId: $session->getId(),
            step: $nextStep,
            messageBody: $messageBody,
            inLineMessageBody: $inLineMessageBody ?? null
        );
    }

    private function handleStepWithAction(
        Message $message,
        AbstractStep $step,
        AbstractTelegramMenuSession $session
    ): void
    {
        if ($step->getBranch() === Branches::EXPERT_CONSULTATION_CANCEL) {
            $cachedConsultation = reset($cachedSessionData[InLineKeyboardParameters::EXPERT_CONSULTATIONS->value]);
            //отменяем консультацию от эксперта
            $this->consultationStorageRepository->changeStatusOfConsultation(consultationId: $cachedConsultation->getId(), statusId: ConsultationStorageRepository::CONSULTATION_STATUSES[4]['id']);
        } elseif ($step->getBranch() === Branches::EXPERT_BALANCE) {
            $expertPayment = $this->expertStorageRepository->getLastWaitingExpertPayment($this->expert->getId());
            if ($expertPayment) {
                $keyboard = Keyboard::remove([
                    'force_reply' => true,
                ]);
                $this->apiClient->sendMessage([
                    'chat_id' => $message->chat->id,
                    'text' => 'Заявка на вывод средств находится на рассмотрении, ожидайте пожалуйста ответа от администрации',
                    'parse_mode' => 'html',
                    'reply_markup' => $keyboard
                ]);
                $this->telegramStorageRepository->closeTelegramSession($session->getId());

                exit();//проверить
            }

            $this->expertStorageRepository->createExpertPayment(
                expertId: $this->expert->getId(),
                amount: $this->expert->getBalance(),
                paymentStatusId: ExpertStorageRepository::EXPERT_PAYMENT_STATUSES[0]['id'],
                paymentStatusName: ExpertStorageRepository::EXPERT_PAYMENT_STATUSES[0]['name']
            );
        }
    }

    private function attemptDeleteLastCallbackMessage(
        AbstractStep $step,
        Message $message
    ): void
    {
        if (
            $step->canInLineKeyboard() &&
            $step->isInLineKeyboardDelete()
        ) {
            $this->apiClient->deleteMessage([
                'chat_id' => $message->chat->id,
                'message_id' => $message->messageId - 1,
            ]);
        }
    }

    private function buildMessageText(
        AbstractStep $step,
        array $cachedSessionData
    ): string
    {
        if (!empty($step->getValuesInText())) {
            if (in_array(TextValues::EXPERT_NAME, $step->getValuesInText())) {
                $text = sprintf($step->getText(), $this->expert->getFirstName());
            } elseif (in_array(TextValues::EXPERT_BALANCE, $step->getValuesInText())) {
                $text = sprintf($step->getText(), $this->expert->getBalance());
            } elseif (in_array(TextValues::CONSULTATION_INFO, $step->getValuesInText())) {
                if ($step->getBranch() === Branches::STUDENT_CONSULTATIONS) {
                    $cachedConsultation = reset($cachedSessionData[InLineKeyboardParameters::STUDENT_CONSULTATIONS->value]);
                } elseif ($step->getBranch() === Branches::EXPERT_CONSULTATIONS) {
                    $cachedConsultation = reset($cachedSessionData[InLineKeyboardParameters::EXPERT_CONSULTATIONS->value]);
                }

//                if (!isset($cachedConsultation)) ;//exception

                $joinText = "\t\t{$cachedConsultation->getExpertConsultationRequest()->getExpert()->getFirstName()} {$cachedConsultation->getExpertConsultationRequest()->getExpert()->getLastName()} {$cachedConsultation->getExpertConsultationRequest()->getExpert()->getPatronymic()} по цене: {$cachedConsultation->getCost()}";
                $text = sprintf($step->getText(), $joinText);
            }
        }

        return $text ?? $step->getText();
    }

    private function buildMessageBody(
        int $chatId,
        AbstractStep $step,
        array $cachedSessionData
    ): array
    {
        $text = $this->buildMessageText(
            step: $step,
            cachedSessionData: $cachedSessionData
        );

        $stepBody = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'html',
        ];

        if (
            $step->getType() === StepTypes::INITIAL_STEP ||
            $step->getType() === StepTypes::STEP
        ) {
            $keyboardButtons = [];
            foreach ($step->getAnswersBag()->getAll() as $answer) {
                $keyboardData = ['text' => $answer->getText()];

                if ($step->canRequestContact()) $keyboardData['request_contact'] = true;

                $keyboardButtons[][] = Keyboard::Button($keyboardData);
            }

            if ($step->canThereBackAnswer()) {
                $keyboardButtons[][] = Keyboard::Button([
                    'text' => TelegramStorageRepository::TEXT_ANSWER_IS_BACK,
                ]);
            }

            $keyboard = Keyboard::make([
                'keyboard' => $keyboardButtons,
                'resize_keyboard' => true,
            ]);

            $stepBody['one_time_keyboard'] = true;
            $stepBody['reply_markup'] = $keyboard;
        } elseif ($step->getType() === StepTypes::FINAL_STEP) {
            $keyboard = Keyboard::remove([
                'force_reply' => true,
            ]);
            $stepBody['reply_markup'] = $keyboard;
        }

        return $stepBody;
    }

    private function buildInLineMessageBody(
        int $chatId,
        AbstractStep $step,
        array $cachedSessionData
    ): array
    {
        $selectedItems = $cachedSessionData[$step->getInLineKeyboardParameter()->value] ?? [];

        $entities = match ($step->getInLineKeyboardParameter()) {
            InLineKeyboardParameters::EXPERT_CONSULTATIONS => $this->consultationStorageRepository->getConsultationsByExpertId(
                expertId: $this->expert->getId(),
                consultationsBag: new ConsultationsBag()
            )->getAll(),
        };

        $keyboardInlineButtons = [];
        foreach ($entities as $entity) {
            $icon = "";
            if (key_exists($entity->getId(), $selectedItems)) $icon = "✔";

            if ($entity instanceof AbstractConsultation) {
                $buttonText = "\t\t{$entity->getConsultationRequest()->getConsultationDateTime()->format('d-m-Y H:i')}" .
                    " - {$entity->getConsultationRequest()->getText()}" .
                    "\n\t\t{$entity->getExpertConsultationRequest()->getExpert()->getFirstName()} " .
                    "{$entity->getExpertConsultationRequest()->getExpert()->getLastName()} {$entity->getExpertConsultationRequest()->getExpert()->getPatronymic()}" .
                    " - консультация: {$entity->getExpertConsultationRequest()->getExpert()->getPriceWorkHour()} руб.";
            }

//            if (!isset($buttonText)) return [];//exception

            $keyboardInlineButtons[][] = Keyboard::inlineButton([
                'text' => "$icon {$buttonText}",
                'callback_data' => json_encode([
                    'id' => $entity->getId(),
                ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            ]);
        }

        $keyboard = Keyboard::make([
            'inline_keyboard' => $keyboardInlineButtons,
            'resize_keyboard' => true,
        ]);

        return [
            'chat_id' => $chatId,
            'text' => $step->getInLineKeyboardText(),
            'parse_mode' => 'html',
            'reply_markup' => $keyboard,
        ];
    }

}
