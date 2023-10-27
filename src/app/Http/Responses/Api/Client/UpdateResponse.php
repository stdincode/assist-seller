<?php

namespace App\Http\Responses\Api\Client;

use Illuminate\Http\JsonResponse;

class UpdateResponse extends JsonResponse
{
    public function __construct(bool $isUpdated, ?string $message = null, $status = 200, $headers = [], $options = 0, bool $json = false)
    {
        if (! $message) {
            if ($isUpdated) {
                $message = 'updated';
            } else {
                $message = 'not updated';
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
