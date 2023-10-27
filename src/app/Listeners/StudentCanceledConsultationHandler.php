<?php

namespace App\Listeners;

use App\Events\StudentCanceledConsultationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Telegram\Bot\Api;

class StudentCanceledConsultationHandler
{
    private Api $apiClient;

    public function __construct(Api $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function handle(StudentCanceledConsultationEvent $event)
    {
        $consultationDateTime = $event->getConsultation()->getConsultationRequest()->getConsultationDateTime()->format('d-m-Y H:00');
        $consultationPlace = $event->getConsultation()->getConsultationRequest()->getPlace()->getName();
        $consultationSpecialization = $event->getConsultation()->getConsultationRequest()->getSpecialization()->getName();
        $consultationText = $event->getConsultation()->getConsultationRequest()->getText();
        $studentFirstName = $event->getConsultation()->getConsultationRequest()->getStudent()->getFirstName();
        $text = "Консультация была отменена учеником
            \n\t\tдата и время: {$consultationDateTime}
            \n\t\tобласть: {$consultationSpecialization}
            \n\t\tплощадка: {$consultationPlace}
            \n\t\tвопрос: {$consultationText}
            \n\t\tученик: {$studentFirstName}";

        $this->apiClient->sendMessage([
            'chat_id' => $event->getChatId(),
            'text' => $text,
            'parse_mode' => 'html',
        ]);
    }
}
