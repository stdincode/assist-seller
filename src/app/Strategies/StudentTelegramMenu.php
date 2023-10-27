<?php

namespace App\Strategies;

use App\DataModels\Entities\AbstractConsultation;
use App\DataModels\Entities\AbstractConsultationRequest;
use App\DataModels\Entities\AbstractExpertConsultationRequest;
use App\DataModels\Entities\AbstractStep;
use App\DataModels\Entities\AbstractStudent;
use App\DataModels\Entities\AbstractTelegramMenuSession;
use App\DataModels\Entities\Bags\ConsultationRequestsBag;
use App\DataModels\Entities\Bags\ConsultationsBag;
use App\DataModels\Entities\Bags\ExpertConsultationRequestsBag;
use App\DataModels\Entities\Bags\PlacesBag;
use App\DataModels\Entities\Bags\SpecializationsBag;
use App\Enums\Branches;
use App\Enums\InLineKeyboardParameters;
use App\Enums\InLineKeyboardParameterTypes;
use App\Enums\MessageParameters;
use App\Enums\StepTypes;
use App\Enums\TextValues;
use App\Events\ExpertConsultationRequestEvent;
use App\Events\StudentCanceledConsultationEvent;
use App\Repositories\ConsultationStorageRepository;
use App\Repositories\ConsultationStorageRepositoryInterface;
use App\Repositories\ExpertStorageRepositoryInterface;
use App\Repositories\Neo4jMenuStorageRepositoryInterface;
use App\Repositories\PlaceStorageRepositoryInterface;
use App\Repositories\SpecializationStorageRepositoryInterface;
use App\Repositories\TelegramStorageRepository;
use App\Repositories\TelegramStorageRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Message;

class StudentTelegramMenu extends AbstractTelegramMenu
{
    private Neo4jMenuStorageRepositoryInterface $neo4jMenuStorageRepository;
    private ExpertStorageRepositoryInterface $expertStorageRepository;
    private ConsultationStorageRepositoryInterface $consultationStorageRepository;
    private AbstractStudent $student;
    private PlaceStorageRepositoryInterface $placeStorageRepository;
    private SpecializationStorageRepositoryInterface $specializationStorageRepository;

    public function __construct(
        AbstractStudent $student,
        Api $apiClient,
        Neo4jMenuStorageRepositoryInterface $neo4jMenuStorageRepository,
        TelegramStorageRepositoryInterface $telegramStorageRepository,
        ExpertStorageRepositoryInterface $expertStorageRepository,
        PlaceStorageRepositoryInterface $placeStorageRepository,
        SpecializationStorageRepositoryInterface $specializationStorageRepository,
        ConsultationStorageRepositoryInterface $consultationStorageRepository
    )
    {
        $this->student = $student;
        $this->neo4jMenuStorageRepository = $neo4jMenuStorageRepository;
        $this->expertStorageRepository = $expertStorageRepository;
        $this->placeStorageRepository = $placeStorageRepository;
        $this->specializationStorageRepository = $specializationStorageRepository;
        $this->consultationStorageRepository = $consultationStorageRepository;

        parent::__construct(
            apiClient: $apiClient,
            telegramStorageRepository: $telegramStorageRepository
        );
    }

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
                    if ($currentStep->getMessageParameter() === MessageParameters::CONSULTATION_REQUEST_TEXT) {
                        $hours = config('telegram.telegram_menu_session_life_time');
                        $sessionClosedDateTime = $session->getCreatedAt()->modify("+{$hours} hours");
                        Cache::put($session->getId(), array_merge($cachedSessionData, [$currentStep->getMessageParameter()->value => $message->text]), $sessionClosedDateTime);
                    }

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

        if (
            $step->getInLineKeyboardParameter() === InLineKeyboardParameters::PLACES ||
            $step->getInLineKeyboardParameter() === InLineKeyboardParameters::SPECIALIZATIONS ||
            $step->getInLineKeyboardParameter() === InLineKeyboardParameters::CONSULTATION_DATE ||
            $step->getInLineKeyboardParameter() === InLineKeyboardParameters::CONSULTATION_TIME ||
            $step->getInLineKeyboardParameter() === InLineKeyboardParameters::STUDENT_CONSULTATION_REQUESTS ||
            $step->getInLineKeyboardParameter() === InLineKeyboardParameters::STUDENT_CONSULTATIONS ||
            $step->getInLineKeyboardParameter() === InLineKeyboardParameters::EXPERT_CONSULTATION_REQUESTS
        ) {
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
            InLineKeyboardParameters::PLACES => $this->placeStorageRepository->getPlaceById($inputEntity['id']),
            InLineKeyboardParameters::SPECIALIZATIONS => $this->specializationStorageRepository->getSpecializationById(id: $inputEntity['id']),
            InLineKeyboardParameters::CONSULTATION_DATE => $this->consultationStorageRepository->getWorkingDayById(id: $inputEntity['id']),
            InLineKeyboardParameters::CONSULTATION_TIME => $this->consultationStorageRepository->getWorkingHourById(
                id: $inputEntity['id'],
                selectedDate: reset($cachedSessionData[InLineKeyboardParameters::CONSULTATION_DATE->value])->getDateTime()
            ),
            InLineKeyboardParameters::STUDENT_CONSULTATION_REQUESTS => $this->consultationStorageRepository->getConsultationRequestById(id: $inputEntity['id']),
            InLineKeyboardParameters::EXPERT_CONSULTATION_REQUESTS => $this->consultationStorageRepository->getExpertConsultationRequestById(id: $inputEntity['id']),
            InLineKeyboardParameters::STUDENT_CONSULTATIONS => $this->consultationStorageRepository->getConsultationById(id: $inputEntity['id']),
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
        if ($step->getBranch() === Branches::STUDENT_CONSULTATION_REQUEST_CANCEL) {
            $cachedConsultationRequest = reset($cachedSessionData[InLineKeyboardParameters::STUDENT_CONSULTATION_REQUESTS->value]);
            //отменяем запрос
            $this->consultationStorageRepository->updateConsultationRequest(
                consultationRequestId: $cachedConsultationRequest->getId(),
                statusId: ConsultationStorageRepository::CONSULTATION_REQUEST_STATUSES[2]['id']
            );

            //отменяем запросы от экспертов
            $expertConsultationRequests = $this->consultationStorageRepository->getExpertConsultationRequestsByConsultationRequestId(
                consultationRequestId: reset($cachedSessionData[InLineKeyboardParameters::STUDENT_CONSULTATION_REQUESTS->value])->getId(),
                expertConsultationRequestsBag: new ExpertConsultationRequestsBag()
            )->getAll();
            $expertConsultationRequestIds = [];
            foreach ($expertConsultationRequests as $expertConsultationRequest) {
                $expertConsultationRequestIds[] = $expertConsultationRequest->getId();
            }
            if (!empty($expertConsultationRequestIds)) {
                $this->consultationStorageRepository->updateExpertConsultationRequests(
                    ids: $expertConsultationRequestIds,
                    statusId: ConsultationStorageRepository::EXPERT_CONSULTATION_REQUEST_STATUSES[2]['id']
                );
            }
        } elseif ($step->getBranch() === Branches::STUDENT_CONSULTATION_REQUEST_SELECT_EXPERT) {
            $cachedConsultationRequest = reset($cachedSessionData[InLineKeyboardParameters::STUDENT_CONSULTATION_REQUESTS->value]);
            //принимаем запрос
            $this->consultationStorageRepository->updateConsultationRequest(
                consultationRequestId: $cachedConsultationRequest->getId(),
                statusId: ConsultationStorageRepository::CONSULTATION_REQUEST_STATUSES[1]['id']
            );

            $cachedExpertConsultationRequest = reset($cachedSessionData[InLineKeyboardParameters::EXPERT_CONSULTATION_REQUESTS->value]);
            //оповещаем и принимаем запрос от выбранного эксперта
            $this->consultationStorageRepository->updateExpertConsultationRequests(
                ids: [$cachedExpertConsultationRequest->getId()],
                statusId: ConsultationStorageRepository::EXPERT_CONSULTATION_REQUEST_STATUSES[1]['id']
            );
            $this->notifyExpertsAboutCreatedConsultation(
                expertId: $cachedExpertConsultationRequest->getExpert()->getId(),
                consultationRequest: $cachedConsultationRequest,
                isSuccessfulRequest: true
            );

            //оповещаем и отменяем запросы от экспертов
            $expertConsultationRequests = $this->consultationStorageRepository->getExpertConsultationRequestsByConsultationRequestId(
                consultationRequestId: reset($cachedSessionData[InLineKeyboardParameters::STUDENT_CONSULTATION_REQUESTS->value])->getId(),
                expertConsultationRequestsBag: new ExpertConsultationRequestsBag()
            )->getAll();
            $expertConsultationRequestIds = [];
            foreach ($expertConsultationRequests as $expertConsultationRequest) {
                if ($cachedExpertConsultationRequest->getId() !== $expertConsultationRequest->getId()) {
                    $this->notifyExpertsAboutCreatedConsultation(
                        expertId: $expertConsultationRequest->getExpert()->getId(),
                        consultationRequest: $cachedConsultationRequest,
                        isSuccessfulRequest: false
                    );
                    $expertConsultationRequestIds[] = $expertConsultationRequest->getId();
                }
            }
            if (!empty($expertConsultationRequestIds)) {
                $this->consultationStorageRepository->updateExpertConsultationRequests(
                    ids: $expertConsultationRequestIds,
                    statusId: ConsultationStorageRepository::EXPERT_CONSULTATION_REQUEST_STATUSES[2]['id']
                );
            }

            //создаем консультацию
            $this->consultationStorageRepository->createConsultation(
                expertConsultationRequest: $cachedExpertConsultationRequest,
                consultationRequest: $cachedConsultationRequest,
                telegramMenuSession: $session
            );
        } elseif ($step->getBranch() === Branches::STUDENT_CONSULTATION_CANCEL) {
            $cachedConsultation = reset($cachedSessionData[InLineKeyboardParameters::STUDENT_CONSULTATIONS->value]);
            //оповещаем эксперта и отменяем консультацию от ученика
            $expert = $this->expertStorageRepository->getExpertById($cachedConsultation->getExpertConsultationRequest()->getExpert()->getId());
            StudentCanceledConsultationEvent::dispatch($expert->getTelegramClient()->getTelegramId(), $cachedConsultation);
            $this->consultationStorageRepository->changeStatusOfConsultation(consultationId: $cachedConsultation->getId(), statusId: ConsultationStorageRepository::CONSULTATION_STATUSES[3]['id']);
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
            if (in_array(TextValues::CONSULTATION_CACHED_INFO, $step->getValuesInText())) {
                $joinText = '';
                foreach ($cachedSessionData as $name => $data) {
                    if (is_array($data)) {
                        $joinText .= "\n\t$name:";
                        foreach ($data as $id => $entity) {
                            $joinText .= "\n\t\t\t\t{$entity->getName()}";
                        }
                    } else {
                        $joinText .= "\n\t$name: $data";
                    }
                }
                $text = sprintf($step->getText(), $joinText);
            } elseif (in_array(TextValues::STUDENT_NAME, $step->getValuesInText())) {
                $text = sprintf($step->getText(), $this->student->getFirstName());
            } elseif (in_array(TextValues::CONSULTATION_REQUEST_INFO, $step->getValuesInText())) {
                $cachedConsultationRequest = reset($cachedSessionData[InLineKeyboardParameters::STUDENT_CONSULTATION_REQUESTS->value]);
                $joinText = "\t\tдата и время: {$cachedConsultationRequest->getConsultationDateTime()->format('d-m-Y H:i')}\n\t\tплощадка: {$cachedConsultationRequest->getPlace()->getName()}\n\t\tспециализация: {$cachedConsultationRequest->getSpecialization()->getName()}\n\t\tтекст: {$cachedConsultationRequest->getText()}";
                $text = sprintf($step->getText(), $joinText);
            } elseif (in_array(TextValues::SELECT_CONSULTATION_REQUEST_EXPERT, $step->getValuesInText())) {
                $cachedExpert = reset($cachedSessionData[InLineKeyboardParameters::EXPERT_CONSULTATION_REQUESTS->value])->getExpert();
                $joinText = "\t\t{$cachedExpert->getFirstName()} {$cachedExpert->getLastName()} {$cachedExpert->getPatronymic()} по цене: {$cachedExpert->getPriceWorkHour()}";
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
            InLineKeyboardParameters::PLACES => $this->placeStorageRepository->getPlaces(new PlacesBag())->getAll(),
            InLineKeyboardParameters::SPECIALIZATIONS => $this->specializationStorageRepository->getSpecializations(new SpecializationsBag())->getAll(),
            InLineKeyboardParameters::CONSULTATION_DATE => $this->consultationStorageRepository->getWorkingDays()->getAll(),
            InLineKeyboardParameters::CONSULTATION_TIME => $this->consultationStorageRepository->getWorkingHours(
                selectedDate: reset($cachedSessionData[InLineKeyboardParameters::CONSULTATION_DATE->value])->getDateTime()
            )->getAll(),
            InLineKeyboardParameters::STUDENT_CONSULTATION_REQUESTS => $this->consultationStorageRepository->getConsultationRequestsByStudent(
                consultationRequestsBag: new ConsultationRequestsBag(),
                student: $this->student
            )->getAll(),
            InLineKeyboardParameters::EXPERT_CONSULTATION_REQUESTS => $this->consultationStorageRepository->getExpertConsultationRequestsByConsultationRequestId(
                consultationRequestId: reset($cachedSessionData[InLineKeyboardParameters::STUDENT_CONSULTATION_REQUESTS->value])->getId(),
                expertConsultationRequestsBag: new ExpertConsultationRequestsBag()
            )->getAll(),
            InLineKeyboardParameters::STUDENT_CONSULTATIONS => $this->consultationStorageRepository->getConsultationsByStudentId(
                studentId: $this->student->getId(),
                consultationsBag: new ConsultationsBag()
            )->getAll(),
        };

        $keyboardInlineButtons = [];
        foreach ($entities as $entity) {
            $icon = "";
            if (key_exists($entity->getId(), $selectedItems)) $icon = "✔";

            if ($entity instanceof AbstractConsultationRequest) {
                $buttonText = "{$entity->getConsultationDateTime()->format('d-m-Y H:i')} - {$entity->getText()}";
            } elseif ($entity instanceof AbstractExpertConsultationRequest) {
                $buttonText = "{$entity->getExpert()->getFirstName()} {$entity->getExpert()->getLastName()} {$entity->getExpert()->getPatronymic()} - консультация: {$entity->getExpert()->getPriceWorkHour()} руб.";
            } elseif ($entity instanceof AbstractConsultation) {
                $buttonText = "\t\t{$entity->getConsultationRequest()->getConsultationDateTime()->format('d-m-Y H:i')}" .
                    " - {$entity->getConsultationRequest()->getText()}" .
                    "\n\t\t{$entity->getExpertConsultationRequest()->getExpert()->getFirstName()} " .
                    "{$entity->getExpertConsultationRequest()->getExpert()->getLastName()} {$entity->getExpertConsultationRequest()->getExpert()->getPatronymic()}" .
                    " - консультация: {$entity->getExpertConsultationRequest()->getExpert()->getPriceWorkHour()} руб.";
            } else {
                $buttonText = $entity->getName();
            }

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

    private function notifyExpertsAboutCreatedConsultation(
        int $expertId,
        AbstractConsultationRequest $consultationRequest,
        bool $isSuccessfulRequest
    ): void
    {
        $expert = $this->expertStorageRepository->getExpertById($expertId);
        $lastTelegramSession = $this->telegramStorageRepository->getLastTelegramSession($expert->getTelegramClient());
        ExpertConsultationRequestEvent::dispatch($lastTelegramSession->getTelegramChatId(), $consultationRequest, $isSuccessfulRequest);
    }

}
