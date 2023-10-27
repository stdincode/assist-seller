<?php

namespace App\Events;

use App\DataModels\Entities\AbstractConsultation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentCanceledConsultationEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private int $chatId;
    private AbstractConsultation $consultation;

    public function __construct(int $chatId, AbstractConsultation $consultation)
    {
        $this->chatId = $chatId;
        $this->consultation = $consultation;
    }

    /**
     * @return int
     */
    public function getChatId(): int
    {
        return $this->chatId;
    }

    /**
     * @return AbstractConsultation
     */
    public function getConsultation(): AbstractConsultation
    {
        return $this->consultation;
    }

}
