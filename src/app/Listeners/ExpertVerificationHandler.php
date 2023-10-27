<?php

namespace App\Listeners;

use App\Events\ExpertVerificationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Telegram\Bot\Api;

class ExpertVerificationHandler
{
    private Api $apiClient;

    public function __construct(Api $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function handle(ExpertVerificationEvent $event): void
    {
        if ($event->isVerificationExpert()) {
            $text = $event->getSuccessfulText();
        } else {
            $text = $event->getNotSuccessfulText();
        }

        $this->apiClient->sendMessage([
            'chat_id' => $event->getChatId(),
            'text' => $text,
            'parse_mode' => 'html',
        ]);
    }
}
