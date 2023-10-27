<?php

namespace App\Events;

use App\DataModels\Entities\AbstractConsultationRequest;
use App\DataModels\Entities\AbstractExpert;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExpertConsultationRequestEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private AbstractConsultationRequest $consultationRequest;
    private bool $isSuccessfulRequest;
    private int $chatId;

    public function __construct(
        int $chatId,
        AbstractConsultationRequest $consultationRequest,
        bool $isSuccessfulRequest
    )
    {
        $this->chatId = $chatId;
        $this->consultationRequest = $consultationRequest;
        $this->isSuccessfulRequest = $isSuccessfulRequest;
    }

    /**
     * @return int
     */
    public function getChatId(): int
    {
        return $this->chatId;
    }

    /**
     * @return AbstractConsultationRequest
     */
    public function getConsultationRequest(): AbstractConsultationRequest
    {
        return $this->consultationRequest;
    }

    /**
     * @return bool
     */
    public function isSuccessfulRequest(): bool
    {
        return $this->isSuccessfulRequest;
    }



}
