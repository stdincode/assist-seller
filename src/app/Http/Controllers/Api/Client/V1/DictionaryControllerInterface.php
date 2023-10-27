<?php

namespace App\Http\Controllers\Api\Client\V1;

use App\Http\Responses\Api\Client\CreateResponse;
use App\Http\Responses\Api\Client\DeleteResponse;
use App\Http\Responses\Api\Client\IndexResponse;
use App\Http\Responses\Api\Client\UpdateResponse;
use Illuminate\Http\Request;

interface DictionaryControllerInterface
{
    public function placesIndex(Request $request): IndexResponse;

    public function placeCreate(Request $request): CreateResponse;

    public function placeUpdate(int $id, Request $request): UpdateResponse;

    public function placeDelete(int $id): DeleteResponse;

    public function specializationsIndex(Request $request): IndexResponse;

    public function specializationCreate(Request $request): CreateResponse;

    public function specializationUpdate(int $id, Request $request): UpdateResponse;

    public function specializationDelete(int $id): DeleteResponse;
}
