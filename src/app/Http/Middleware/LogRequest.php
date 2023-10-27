<?php

namespace App\Http\Middleware;

use App\Constants\ClientApi;
use App\Http\Responses\Api\Client\File\FilesResponse;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class LogRequest
{
    public const MAX_COLLECTION_ITEMS = 10;

    public function handle(Request $request, Closure $next)
    {
        $this->logRequest($request);
        $response = $next($request);
        $this->logResponse($response);

        return $response;
    }

    private function logRequest(Request $request): void
    {
        $requestContext = [
            'request_id' => config(ClientApi::REQUEST_ID),
            'method'     => $request->getMethod(),
            'path'       => $request->getPathInfo(),
            'ip'         => $request->ip(),
            'client_id'  => $request->header(ClientApi::HEADER_CLIENT_ID),
            'input'      => $request->input(),
        ];
        $files = [];
        foreach ($request->allFiles() as $file) {
            /**
             * @var $file \Illuminate\Http\UploadedFile
             */
            $files[] = [
                'filename' => $file->getClientOriginalName(),
                'size'     => $file->getSize(),
            ];
        }
        $requestContext['files'] = $files;
        Log::info('REQUEST', $requestContext);
    }

    private function logResponse(Response|JsonResponse $response): void
    {
        $responseContext = [
            'request_id'  => config(ClientApi::REQUEST_ID),
            'http_status' => $response->getStatusCode(),
        ];

        $message = 'RESPONSE';

        $content = $response->getOriginalContent();
        if ($response instanceof FilesResponse) {
            // Отразить в логах ограниченное количество элементов, чтобы не генерировать огромные строки
            $items = $content['result']['items'];
            if (count($items) > self::MAX_COLLECTION_ITEMS) {
                $message .= ' (Показано только ' . self::MAX_COLLECTION_ITEMS . ' элементов из ' . count($items) . ')';
            }

            $content['result']['items'] = array_slice($content['result']['items'], 0, 10);
            $responseContext['content'] = $content;
        } else {
            $responseContext = is_array($content) ? $content : $response->getContent();
        }
        Log::info($message, $responseContext);
    }
}
