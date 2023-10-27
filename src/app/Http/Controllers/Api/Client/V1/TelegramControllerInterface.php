<?php

namespace App\Http\Controllers\Api\Client\V1;

use App\Http\Responses\Api\Client\CreateResponse;
use App\Http\Responses\Api\Client\DeleteResponse;
use App\Http\Responses\Api\Client\IndexResponse;
use App\Http\Responses\Api\Client\ShowResponse;
use App\Http\Responses\Api\Client\UpdateResponse;
use Illuminate\Http\Request;

interface TelegramControllerInterface
{
    public function telegramClientCreate(Request $request): CreateResponse;

}
