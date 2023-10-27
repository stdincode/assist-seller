<?php

namespace App\Strategies;

use App\DataModels\Entities\AbstractStep;
use App\DataModels\Entities\AbstractTelegramMenuSession;
use App\Enums\StepTypes;
use App\Repositories\TelegramStorageRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Message;

abstract class AbstractTelegramMenu
{
    protected Api $apiClient;
    protected TelegramStorageRepositoryInterface $telegramStorageRepository;

    public function __construct(
        Api $apiClient,
        TelegramStorageRepositoryInterface $telegramStorageRepository
    )
    {
        $this->telegramStorageRepository = $telegramStorageRepository;
        $this->apiClient = $apiClient;
    }

    abstract public function messageHandler(
        Message $message,
        AbstractStep $currentStep,
        AbstractTelegramMenuSession $session
    );
    abstract public function callbackMessageHandler(
        CallbackQuery $callbackQuery,
        AbstractStep $step,
        AbstractTelegramMenuSession $session
    );

    protected function sendStep(
        int $chatId,
        int $messageId,
        int $sessionId,
        AbstractStep $step,
        array $messageBody,
        ?array $inLineMessageBody = null
    )
    {
        $this->telegramStorageRepository->saveTelegramClientStep(
            stepId: $step->getId(),
            sessionId: $sessionId,
            telegramMessageId: $messageId
        );

        $this->apiClient->sendMessage($messageBody);

        //отправляем файлы
        if (!empty($step->getFilesInText())) $this->sendFiles(chatId: $chatId, fileNames: $step->getFilesInText());

        if ($step->canInLineKeyboard() && $inLineMessageBody) {
            $this->apiClient->sendMessage($inLineMessageBody);
        }
    }

    protected function sendCallbackStep(
        int                      $chatId,
        string                   $inLineText,
        array                    $keyboardInLineButtons
    ): void
    {
        $keyboard = Keyboard::make([
            'inline_keyboard' => $keyboardInLineButtons,
            'resize_keyboard' => true,
        ]);

        $this->apiClient->sendMessage([
            'chat_id' => $chatId,
            'text' => $inLineText,
            'parse_mode' => 'html',
            'reply_markup' => $keyboard,
        ]);

    }

    protected function sendFiles(int $chatId, array $fileNames)
    {
        foreach ($fileNames as $fileName) {
            $fileUrl = storage_path() . '/app/public/' . $fileName->value;

            $this->apiClient->sendDocument([
                'chat_id' => $chatId,
                'document' => InputFile::create(file: $fileUrl),
            ]);
        }
    }

    protected function isStepParametersCached(
        AbstractStep $step,
        array $cachedSessionData
    ): bool
    {
        Log::info('TEST', [$step->isInLineKeyboardParameterRequired()]);

        if (
            $step->canMessageParameter() &&
            $step->isMessageParameterRequired()
        ) {
            if (!array_key_exists($step->getMessageParameter()->value, $cachedSessionData)) {
                //send message 'заполните пож поле'?
                return false;
            }
        }

        if (
            $step->canInLineKeyboard() &&
            $step->isInLineKeyboardParameterRequired()
        ) {
            if (!array_key_exists($step->getInLineKeyboardParameter()->value, $cachedSessionData )) return false;
            if (empty($cachedSessionData[$step->getInLineKeyboardParameter()->value])) return false;
        }

        return true;
    }
}
