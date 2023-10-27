<?php

namespace App\Http\Responses\Api\Client;

use App\DataModels\Entities\EntityInterface;
use Illuminate\Http\JsonResponse;

class ShowResponse extends JsonResponse
{
    public function __construct(?EntityInterface $entity, $status = 200, $headers = [], $options = 0, bool $json = false)
    {
        if ($entity) {
            $result = $entity->asArray();
            $message = 'found';
        } else {
            $result = [];
            $message = 'not found';
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
