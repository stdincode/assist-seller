<?php

namespace App\Http\Responses\Api\Client;

use App\DataModels\Entities\EntityInterface;
use Illuminate\Http\JsonResponse;

class CreateResponse extends JsonResponse
{
    public function __construct(?EntityInterface $entity, $status = 200, $headers = [], $options = 0, bool $json = false)
    {
        if ($entity) {
            $result = $entity->asArray();
            $message = 'created';
        } else {
            $result = [];
            $message = 'not created';
        }

        parent::__construct(
            data: [
                'result' => $result,
                'message' => $message
            ],
            status: $status,
            headers: $headers,
            options: $options,
            json: $json
        );
    }
}
