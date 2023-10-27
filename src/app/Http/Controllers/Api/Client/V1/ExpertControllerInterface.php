<?php

namespace App\Http\Controllers\Api\Client\V1;

use App\Http\Responses\Api\Client\CreateResponse;
use App\Http\Responses\Api\Client\DeleteResponse;
use App\Http\Responses\Api\Client\IndexResponse;
use App\Http\Responses\Api\Client\ShowResponse;
use App\Http\Responses\Api\Client\UpdateResponse;
use Illuminate\Http\Request;

interface ExpertControllerInterface
{
    public function expertsIndex(Request $request): IndexResponse;

    public function expertShow(int $id, Request $request): ShowResponse;

    public function expertCreate(Request $request): CreateResponse;

    public function expertUpdate(int $id, Request $request): UpdateResponse;

    public function expertDelete(int $id): DeleteResponse;

    public function expertPaymentStatusesIndex(): IndexResponse;

    public function expertPaymentsIndex(int $expertId, Request $request): IndexResponse;

    public function expertPaymentCreate(int $expertId, Request $request): CreateResponse;

    public function expertPaymentUpdate(int $expertId, int $expertPaymentId, Request $request): UpdateResponse;

}
