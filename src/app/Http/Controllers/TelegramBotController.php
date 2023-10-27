<?php

namespace App\Http\Controllers;

use App\Services\TelegramServiceInterface;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class TelegramBotController
{
    private TelegramServiceInterface $telegramService;

    public function __construct(TelegramServiceInterface $telegramService)
    {
        $this->telegramService = $telegramService;
    }

    public function setWebHook()
    {
        try {
            $result = $this->telegramService->setWebhook();
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return response($exception->getMessage(), 400);
        }

        return response($result);
    }

    public function handleWebhook(): void
    {
        try {
            $this->telegramService->handleWebhooks();
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

}
