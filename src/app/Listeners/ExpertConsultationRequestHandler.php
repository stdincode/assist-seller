<?php

namespace App\Listeners;

use App\Events\ExpertConsultationRequestEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Telegram\Bot\Api;

class ExpertConsultationRequestHandler
{
    private Api $apiClient;

    public function __construct(Api $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function handle(ExpertConsultationRequestEvent $event)
    {
        if ($event->isSuccessfulRequest()) {
            $text = "Консультация успешно подтверждена учеником\n
                \tдата и время: {$event->getConsultationRequest()->getConsultationDateTime()->format('d-m-Y H:00')}
                \tобласть: {$event->getConsultationRequest()->getSpecialization()->getName()}
                \tплощадка: {$event->getConsultationRequest()->getPlace()->getName()}
                \tвопрос: {$event->getConsultationRequest()->getText()}
                \n\nЗа час до встречи Вам будет отправлена ссылка на телеконференцию";
        } else {
            $text = "К сожалению ученик выбрал другого эксперта по консультации:\n
                \t\tдата и время: {$event->getConsultationRequest()->getConsultationDateTime()->format('d-m-Y H:00')}
                \t\t\область: {$event->getConsultationRequest()->getSpecialization()->getName()}
                \t\t\площадка: {$event->getConsultationRequest()->getPlace()->getName()}
                \t\t\вопрос: {$event->getConsultationRequest()->getText()}";
        }

        $this->apiClient->sendMessage([
            'chat_id' => $event->getChatId(),
            'text' => $text,
            'parse_mode' => 'html',
        ]);
    }
}
