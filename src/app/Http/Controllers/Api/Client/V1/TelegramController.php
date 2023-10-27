<?php

namespace App\Http\Controllers\Api\Client\V1;

use App\Http\Responses\Api\Client\CreateResponse;
use App\Http\Responses\Api\Client\DeleteResponse;
use App\Http\Responses\Api\Client\IndexResponse;
use App\Http\Responses\Api\Client\ShowResponse;
use App\Http\Responses\Api\Client\UpdateResponse;
use App\Services\TelegramServiceInterface;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TelegramController extends ClientApiController implements TelegramControllerInterface
{
    private TelegramServiceInterface $service;

    public function __construct(TelegramServiceInterface $service)
    {
        $this->service = $service;
    }

    #[OA\Post(path: '/api/v1/telegram_clients/', description: 'Создание telegram клиента', security: [['bearerAuth' => []]], tags: ['telegram_clients'])]
    #[OA\RequestBody(ref: '#/components/requestBodies/telegramClientCreateRequest')]
    #[OA\Response(ref: '#/components/responses/telegramClientCreateResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function telegramClientCreate(Request $request): CreateResponse
    {
        $params = $request->post();
        $telegramId = $params['telegram_id'];
        $firstName = $params['first_name'] ?? null;
        $lastName = $params['last_name'] ?? null;
        $username = $params['username'] ?? null;

        $student = $this->service->createTelegramClient(
            telegramId: $telegramId,
            firstName: $firstName,
            lastName: $lastName,
            username: $username
        );

        return new CreateResponse($student);
    }

}
