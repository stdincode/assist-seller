<?php

namespace App\Http\Controllers\Api\Client\V1;

use App\Http\Responses\Api\Client\CreateResponse;
use App\Http\Responses\Api\Client\DeleteResponse;
use App\Http\Responses\Api\Client\IndexResponse;
use App\Http\Responses\Api\Client\ShowResponse;
use App\Http\Responses\Api\Client\UpdateResponse;
use App\Services\StudentServiceInterface;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class StudentController extends ClientApiController implements StudentControllerInterface
{
    private StudentServiceInterface $service;

    public function __construct(StudentServiceInterface $service)
    {
        $this->service = $service;
    }

    #[OA\Get(path: '/api/v1/students', description: 'Список студентов', security: [['bearerAuth' => []]], tags: ['students'])]
    #[OA\Response(ref: '#/components/responses/studentsIndexResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function studentsIndex(Request $request): IndexResponse
    {
        $students = $this->service->getAllStudents();

        return new IndexResponse($students);
    }

    #[OA\Get(path: '/api/v1/students/{id}', description: 'Информация по студенту', security: [['bearerAuth' => []]], tags: ['students'])]
    #[OA\Parameter(name: 'id', description: 'Идентификатор студента', in: 'path', required: true, schema: new OA\Schema(type: 'integer', format: 'integer'))]
    #[OA\Response(ref: '#/components/responses/studentShowResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function studentShow(int $id, Request $request): ShowResponse
    {
        $student = $this->service->getStudent(id: $id);

        return new ShowResponse($student);
    }

    #[OA\Post(path: '/api/v1/students', description: 'Создание студента', security: [['bearerAuth' => []]], tags: ['students'])]
    #[OA\RequestBody(ref: '#/components/requestBodies/studentCreateRequest')]
    #[OA\Response(ref: '#/components/responses/studentCreateResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function studentCreate(Request $request): CreateResponse
    {
        $params = $request->post();
        $firstName = $params['first_name'];
        $telegramClientId = $params['telegram_client_id'];
        $contactPhoneNumber = $params['contact_phone_number'] ?? null;

        $student = $this->service->createStudent(
            firstName: $firstName,
            telegramClientId: $telegramClientId,
            contactPhoneNumber: $contactPhoneNumber
        );

        return new CreateResponse($student);
    }

    #[OA\Post(path: '/api/v1/students/{id}', description: 'Обновление студента', security: [['bearerAuth' => []]], tags: ['students'])]
    #[OA\Parameter(name: 'id', description: 'Идентификатор студента', in: 'path', required: true, schema: new OA\Schema(type: 'integer', format: 'integer'))]
    #[OA\RequestBody(ref: '#/components/requestBodies/studentUpdateRequest')]
    #[OA\Response(ref: '#/components/responses/updateResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function studentUpdate(int $id, Request $request): UpdateResponse
    {
        $params = $request->post();
        $firstName = $params['first_name'] ?? null;
        $contactPhoneNumber = $params['contact_phone_number'] ?? null;
        $isBlocked = $params['is_blocked'] ?? null;

        $result = $this->service->updateStudent(
            id: $id,
            firstName: $firstName,
            contactPhoneNumber: $contactPhoneNumber,
            isBlocked: $isBlocked
        );

        return new UpdateResponse($result);
    }

    #[OA\Delete(path: '/api/v1/students/{id}', description: 'Удаление студента', security: [['bearerAuth' => []]], tags: ['students'])]
    #[OA\Parameter(name: 'id', description: 'Идентификатор студента', in: 'path', required: true, schema: new OA\Schema(type: 'integer', format: 'integer'))]
    #[OA\Response(ref: '#/components/responses/deleteResponse', response: 200)]
    #[OA\Response(ref: '#/components/responses/serverError', response: 500)]
    public function studentDelete(int $id): DeleteResponse
    {
        $result = $this->service->deleteStudent(id: $id);

        return new DeleteResponse($result);
    }

}
