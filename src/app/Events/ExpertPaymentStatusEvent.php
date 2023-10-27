<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExpertPaymentStatusEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private int $chatId;
    private bool $isExpertPaymentApproved;

    public function __construct(int $chatId, bool $isExpertPaymentApproved)
    {
        $this->chatId = $chatId;
        $this->isExpertPaymentApproved = $isExpertPaymentApproved;
    }

    public function getChatId(): int
    {
        return $this->chatId;
    }

    public function isExpertPaymentApproved(): bool
    {
        return $this->isExpertPaymentApproved;
    }

    public function getApprovedText(): string
    {
        return "Заявка на вывод средств одобрена.";
    }

    public function getUnapprovedText(): string
    {
        return "Заявка на вывод средств отклонена.";
    }
}
