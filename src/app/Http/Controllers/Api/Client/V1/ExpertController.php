<?php

namespace App\Http\Controllers\Api\Client\V1;

use App\Http\Responses\Api\Client\CreateResponse;
use App\Http\Responses\Api\Client\DeleteResponse;
use App\Http\Responses\Api\Client\IndexResponse;
use App\Http\Responses\Api\Client\ShowResponse;
use App\Http\Responses\Api\Client\UpdateResponse;
use App\Services\DictionaryServiceInterface;
use App\Services\ExpertServiceInterface;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ExpertController extends ClientApiController implements ExpertControllerInterface
{
    private ExpertServiceInterface $expertService;
    private DictionaryServiceInterface $dictionaryService;

    public function __construct(
        ExpertServiceInterface $expertService,
        DictionaryServiceInterface $dictionaryService
    )
    {
        $this->expertService = $expertService;
        $this->dictionaryService = $dictionaryService;
    }

    #[OA\Get(path: '/api/v1/experts', description: 'Список экспертов', security: [['bearerAuth' => []]], tags: ['experts'])]
    #[OA\Response(ref: '#/components/responses/expertsIndexResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function expertsIndex(Request $request): IndexResponse
    {
        $experts = $this->expertService->getAllExperts();

        return new IndexResponse($experts);
    }

    #[OA\Get(path: '/api/v1/experts/{id}', description: 'Информация по эксперту', security: [['bearerAuth' => []]], tags: ['experts'])]
    #[OA\Parameter(name: 'id', description: 'Идентификатор эксперта', in: 'path', required: true, schema: new OA\Schema(type: 'integer', format: 'integer'))]
    #[OA\Response(ref: '#/components/responses/expertShowResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function expertShow(int $id, Request $request): ShowResponse
    {
        $expert = $this->expertService->getExpert(id: $id);

        return new ShowResponse($expert);
    }

    #[OA\Post(path: '/api/v1/experts', description: 'Создание эксперта', security: [['bearerAuth' => []]], tags: ['experts'])]
    #[OA\RequestBody(ref: '#/components/requestBodies/expertCreateRequest')]
    #[OA\Response(ref: '#/components/responses/expertCreateResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function expertCreate(Request $request): CreateResponse
    {
        $params = $request->post();
        $firstName = $params['first_name'];
        $lastName = $params['last_name'];
        $patronymic = $params['patronymic'];
        $biography = $params['biography'];
        $telegramClientId = $params['telegram_client_id'];
        $telegramPhoneNumber = $params['telegram_phone_number'];
        $whatsappPhoneNumber = $params['whatsapp_phone_number'] ?? null;
        $priceWorkHour = $params['price_work_hour'];
        $requisites = $params['requisites'];
        $placeIds = $params['place_ids'] ?? [];
        $specializationIds = $params['specialization_ids'] ?? [];

        $avatar = $request->file()['avatar'] ?? null;
        $video = $request->file()['video'] ?? null;

        if ($placeIds) $this->dictionaryService->checkExistsPlaces($placeIds);
        if ($specializationIds) $this->dictionaryService->checkExistsSpecializations($specializationIds);

        $expert = $this->expertService->createExpert(
            firstName: $firstName,
            lastName: $lastName,
            patronymic: $patronymic,
            biography: $biography,
            telegramClientId: $telegramClientId,
            telegramPhoneNumber: $telegramPhoneNumber,
            whatsappPhoneNumber: $whatsappPhoneNumber,
            priceWorkHour: $priceWorkHour,
            requisites: $requisites,
            uploadedAvatar: $avatar,
            uploadedVideo: $video,
            placeIds: $placeIds,
            specializationIds: $specializationIds
        );

        return new CreateResponse($expert);
    }

    #[OA\Post(path: '/api/v1/experts/{id}', description: 'Обновление эксперта', security: [['bearerAuth' => []]], tags: ['experts'])]
    #[OA\Parameter(name: 'id', description: 'Идентификатор эксперта', in: 'path', required: true, schema: new OA\Schema(type: 'integer', format: 'integer'))]
    #[OA\RequestBody(ref: '#/components/requestBodies/expertUpdateRequest')]
    #[OA\Response(ref: '#/components/responses/updateResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function expertUpdate(int $id, Request $request): UpdateResponse
    {
        $params = $request->post();
        $firstname = $params['first_name'] ?? null;
        $lastname = $params['last_name'] ?? null;
        $patronymic = $params['patronymic'] ?? null;
        $biography = $params['biography'] ?? null;
        $telegramPhoneNumber = $params['telegram_phone_number'] ?? null;
        $whatsappPhoneNumber = $params['whatsapp_phone_number'] ?? null;
        $priceWorkHour = $params['price_work_hour'] ?? null;
        $requisites = $params['requisites'] ?? null;
        $balance = $params['balance'] ?? null;
        $isVerification = $params['is_verification'] ?? null;
        $isBlocked = $params['is_blocked'] ?? null;
        $placeIds = $params['place_ids'] ?? null;
        $specializationIds = $params['specialization_ids'] ?? null;

        $avatar = $request->file()['avatar'] ?? null;
        $video = $request->file()['video'] ?? null;

        $result = $this->expertService->updateExpert(
            id: $id,
            firstName: $firstname,
            lastName: $lastname,
            patronymic: $patronymic,
            biography: $biography,
            uploadedAvatar: $avatar,
            uploadedVideo: $video,
            telegramPhoneNumber: $telegramPhoneNumber,
            whatsappPhoneNumber: $whatsappPhoneNumber,
            priceWorkHour: $priceWorkHour,
            requisites: $requisites,
            balance: $balance,
            isVerification: $isVerification,
            isBlocked: $isBlocked,
            placeIds: $placeIds,
            specializationIds: $specializationIds
        );

        return new UpdateResponse($result);
    }

    #[OA\Delete(path: '/api/v1/experts/{id}', description: 'Удаление эксперта', security: [['bearerAuth' => []]], tags: ['experts'])]
    #[OA\Parameter(name: 'id', description: 'Идентификатор эксперта', in: 'path', required: true, schema: new OA\Schema(type: 'integer', format: 'integer'))]
    #[OA\Response(ref: '#/components/responses/deleteResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function expertDelete(int $id): DeleteResponse
    {
        $result = $this->expertService->deleteExpert(id: $id);

        return new DeleteResponse($result);
    }

    #[OA\Get(path: '/api/v1/expert_payment_statuses', description: 'Список статусов выплат эксперту', security: [['bearerAuth' => []]], tags: ['experts'])]
    #[OA\Response(ref: '#/components/responses/expertPaymentStatusesIndexResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function expertPaymentStatusesIndex(): IndexResponse
    {
        $expertPaymentStatuses = $this->expertService->getExpertPaymentStatuses();

        return new IndexResponse($expertPaymentStatuses);
    }

    #[OA\Get(path: '/api/v1/experts/{id}/payments', description: 'Список выплат эксперту', security: [['bearerAuth' => []]], tags: ['experts'])]
    #[OA\Parameter(name: 'id', description: 'Идентификатор эксперта', in: 'path', required: true, schema: new OA\Schema(type: 'integer', format: 'integer'))]
    #[OA\Response(ref: '#/components/responses/expertPaymentsIndexResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function expertPaymentsIndex(int $expertId, Request $request): IndexResponse
    {
        $expertPaymentsBag = $this->expertService->getExpertPayments(expertId: $expertId);

        return new IndexResponse($expertPaymentsBag);
    }

    #[OA\Post(path: '/api/v1/experts/{id}/payments', description: 'Создание выплаты эксперту', security: [['bearerAuth' => []]], tags: ['experts'])]
    #[OA\Parameter(name: 'id', description: 'Идентификатор эксперта', in: 'path', required: true, schema: new OA\Schema(type: 'integer', format: 'integer'))]
    #[OA\Response(ref: '#/components/responses/expertPaymentCreateResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function expertPaymentCreate(int $expertId, Request $request): CreateResponse
    {
        $expertPayment = $this->expertService->createExpertPayment(expertId: $expertId);

        return new CreateResponse($expertPayment);
    }

    #[OA\Post(path: '/api/v1/experts/{id}/payments/{payment_id}', description: 'Обновление статуса выплаты эксперту', security: [['bearerAuth' => []]], tags: ['experts'])]
    #[OA\Parameter(name: 'id', description: 'Идентификатор эксперта', in: 'path', required: true, schema: new OA\Schema(type: 'integer', format: 'integer'))]
    #[OA\Parameter(name: 'payment_id', description: 'Идентификатор выплаты эксперту', in: 'path', required: true, schema: new OA\Schema(type: 'integer', format: 'integer'))]
    #[OA\RequestBody(ref: '#/components/requestBodies/expertPaymentUpdateRequest')]
    #[OA\Response(ref: '#/components/responses/updateResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function expertPaymentUpdate(int $expertId, int $expertPaymentId, Request $request): UpdateResponse
    {
        $params = $request->post();
        $statusId = $params['status_id'];

        $result = $this->expertService->updateExpertPayment(
            expertId: $expertId,
            expertPaymentId: $expertPaymentId,
            statusId: $statusId
        );

        return new UpdateResponse($result);
    }
}
