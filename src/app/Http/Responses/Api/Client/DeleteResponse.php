<?php

namespace App\Http\Responses\Api\Client;

use Illuminate\Http\JsonResponse;

class DeleteResponse extends JsonResponse
{
    public function __construct(bool $isDeleted, ?string $message = null, $status = 200, $headers = [], $options = 0, bool $json = false)
    {
        if (! $message) {
            if ($isDeleted) {
                $message = 'deleted';
            } else {
                $message = 'not deleted';
            }
        }

        parent::__construct(
            data: [
                'message' => $message,
            ],
            status: $status,
            headers: $headers,
            options: $options,
            json: $json
        );
    }
}
