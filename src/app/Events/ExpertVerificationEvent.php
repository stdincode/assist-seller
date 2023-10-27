<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExpertVerificationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private int $chatId;
    private bool $isVerificationExpert;

    public function __construct(int $chatId, bool $isVerificationExpert)
    {
        $this->chatId = $chatId;
        $this->isVerificationExpert = $isVerificationExpert;
    }

    public function getChatId(): int
    {
        return $this->chatId;
    }

    public function isVerificationExpert(): bool
    {
        return $this->isVerificationExpert;
    }

    public function getSuccessfulText(): string
    {
        return "Поздравляю, вы зарегистрированы как эксперт!\n\t\t+Правила платформы";
    }

    public function getNotSuccessfulText(): string
    {
        return "К сожалению, сейчас мы не можем взять вас как эксперта.\n\nЖдем вас в следующий раз, спасибо.";
    }

//    /**
//     * Get the channels the event should broadcast on.
//     *
//     * @return \Illuminate\Broadcasting\Channel|array
//     */
//    public function broadcastOn()
//    {
//        return new PrivateChannel('channel-name');
//    }
}
