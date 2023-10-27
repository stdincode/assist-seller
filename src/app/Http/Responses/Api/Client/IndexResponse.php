<?php

namespace App\Http\Responses\Api\Client;

use App\DataModels\Entities\Bags\AbstractBag;
use App\DataModels\Entities\EntityInterface;
use Illuminate\Http\JsonResponse;

class IndexResponse extends JsonResponse
{
    public function __construct(AbstractBag $bag, $status = 200, $headers = [], $options = 0, bool $json = false)
    {
        if (!empty($bag->getAll())) {
            $result = array_map(
                fn (EntityInterface $entity) => $entity->asArray(),
                $bag->getAll()
            );
            $message = 'found';
        } else {
            $result = [];
            $message = 'not found';
        }

        parent::__construct(
            data: [
                'result' => $result,
                'total' => $bag->getTotal(),
                'message' => $message
            ],
            status: $status,
            headers: $headers,
            options: $options,
            json: $json
        );
    }
}
