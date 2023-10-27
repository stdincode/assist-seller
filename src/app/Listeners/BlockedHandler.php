<?php

namespace App\Listeners;

use App\Events\BlockedEvent;
use Telegram\Bot\Api;

class BlockedHandler
{
    private Api $apiClient;

    public function __construct(Api $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function handle(BlockedEvent $event)
    {
        $text = 'Вы заблокированы';

        $this->apiClient->sendMessage([
            'chat_id' => $event->getChatId(),
            'text' => $text,
            'parse_mode' => 'html',
        ]);
    }
}
