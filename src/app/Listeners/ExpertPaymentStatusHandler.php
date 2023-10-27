<?php

namespace App\Listeners;

use App\Events\ExpertPaymentStatusEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Telegram\Bot\Api;

class ExpertPaymentStatusHandler
{
    private Api $apiClient;

    public function __construct(Api $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function handle(ExpertPaymentStatusEvent $event): void
    {
        if ($event->isExpertPaymentApproved()) {
            $text = $event->getApprovedText();
        } else {
            $text = $event->getUnapprovedText();
        }

        $this->apiClient->sendMessage([
            'chat_id' => $event->getChatId(),
            'text' => $text,
            'parse_mode' => 'html',
        ]);
    }
}
