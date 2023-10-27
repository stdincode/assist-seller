<?php

namespace App\Http\Controllers\Api\Client\V1;

use App\Http\Responses\Api\Client\CreateResponse;
use App\Http\Responses\Api\Client\DeleteResponse;
use App\Http\Responses\Api\Client\IndexResponse;
use App\Http\Responses\Api\Client\UpdateResponse;
use App\Services\DictionaryServiceInterface;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class DictionaryController extends ClientApiController implements DictionaryControllerInterface
{
    private DictionaryServiceInterface $service;

    public function __construct(DictionaryServiceInterface $service)
    {
        $this->service = $service;
    }

    #[OA\Get(path: '/api/v1/places', description: 'Список площадок', security: [['bearerAuth' => []]], tags: ['places'])]
    #[OA\Response(ref: '#/components/responses/placesIndexResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function placesIndex(Request $request): IndexResponse
    {
        $places = $this->service->getAllPlaces();

        return (new IndexResponse($places));
    }

    #[OA\Post(path: '/api/v1/places', description: 'Создание площадки', security: [['bearerAuth' => []]], tags: ['places'])]
    #[OA\RequestBody(ref: '#/components/requestBodies/placeRequest')]
    #[OA\Response(ref: '#/components/responses/placeCreateResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function placeCreate(Request $request): CreateResponse
    {
        $params = $request->post();
        $name = $params['name'];

        $place = $this->service->createPlace($name);

        return new CreateResponse($place);
    }

    #[OA\Post(path: '/api/v1/places/{id}', description: 'Обновление площадки', security: [['bearerAuth' => []]], tags: ['places'])]
    #[OA\Parameter(name: 'id', description: 'Идентификатор площадки', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'integer'))]
    #[OA\RequestBody(ref: '#/components/requestBodies/placeRequest')]
    #[OA\Response(ref: '#/components/responses/updateResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function placeUpdate(int $id, Request $request): UpdateResponse
    {
        $params = $request->post();
        $name = $params['name'];

        $result = $this->service->updatePlace($id, $name);

        return new UpdateResponse($result);
    }

    #[OA\Delete(path: '/api/v1/places/{id}', description: 'Удаление площадки', security: [['bearerAuth' => []]], tags: ['places'])]
    #[OA\Parameter(name: 'id', description: 'Идентификатор площадки', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'integer'))]
    #[OA\Response(ref: '#/components/responses/deleteResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function placeDelete(int $id): DeleteResponse
    {
        $result = $this->service->deletePlace($id);

        return new DeleteResponse($result);
    }

    #[OA\Get(path: '/api/v1/specializations', description: 'Список специализаций', security: [['bearerAuth' => []]], tags: ['specializations'])]
    #[OA\Response(ref: '#/components/responses/specializationsIndexResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function specializationsIndex(Request $request): IndexResponse
    {
        $specialization = $this->service->getAllSpecializations();

        return (new IndexResponse($specialization));
    }

    #[OA\Post(path: '/api/v1/specializations', description: 'Создание специализации', security: [['bearerAuth' => []]], tags: ['specializations'])]
    #[OA\RequestBody(ref: '#/components/requestBodies/specializationRequest')]
    #[OA\Response(ref: '#/components/responses/specializationCreateResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function specializationCreate(Request $request): CreateResponse
    {
        $params = $request->post();
        $name = $params['name'];

        $specialization = $this->service->createSpecialization($name);

        return new CreateResponse($specialization);
    }

    #[OA\Post(path: '/api/v1/specializations/{id}', description: 'Обновление специализации', security: [['bearerAuth' => []]], tags: ['specializations'])]
    #[OA\RequestBody(ref: '#/components/requestBodies/specializationRequest')]
    #[OA\Response(ref: '#/components/responses/updateResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function specializationUpdate(int $id, Request $request): UpdateResponse
    {
        $params = $request->post();
        $name = $params['name'];

        $result = $this->service->updatePlace($id, $name);

        return new UpdateResponse($result);
    }

    #[OA\Delete(path: '/api/v1/specializations/{id}', description: 'Удаление специализации', security: [['bearerAuth' => []]], tags: ['specializations'])]
    #[OA\Parameter(name: 'id', description: 'Идентификатор специализации', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'integer'))]
    #[OA\Response(ref: '#/components/responses/deleteResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function specializationDelete(int $id): DeleteResponse
    {
        $result = $this->service->deleteSpecialization($id);

        return new DeleteResponse($result);
    }
}
