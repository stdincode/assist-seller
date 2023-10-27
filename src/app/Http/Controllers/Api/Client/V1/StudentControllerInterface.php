<?php

namespace App\Http\Controllers\Api\Client\V1;

use App\Http\Responses\Api\Client\CreateResponse;
use App\Http\Responses\Api\Client\DeleteResponse;
use App\Http\Responses\Api\Client\IndexResponse;
use App\Http\Responses\Api\Client\ShowResponse;
use App\Http\Responses\Api\Client\UpdateResponse;
use Illuminate\Http\Request;

interface StudentControllerInterface
{
    public function studentsIndex(Request $request): IndexResponse;

    public function studentShow(int $id, Request $request): ShowResponse;

    public function studentCreate(Request $request): CreateResponse;

    public function studentUpdate(int $id, Request $request): UpdateResponse;

    public function studentDelete(int $id): DeleteResponse;

}
