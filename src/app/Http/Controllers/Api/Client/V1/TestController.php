<?php

namespace App\Http\Controllers\Api\Client\V1;

use App\Http\Responses\Api\Client\CreateResponse;
use App\Http\Responses\Api\Client\DeleteResponse;
use App\Http\Responses\Api\Client\IndexResponse;
use App\Http\Responses\Api\Client\ShowResponse;
use App\Http\Responses\Api\Client\UpdateResponse;
use App\Services\TelegramServiceInterface;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TestController
{
    public function test(Request $request)
    {
        $array = [];
        for ($i = 1; $i <= 100; $i++) {
            $array[] = rand(1, 100);
        }

        function quickSort($array) {
            if (count($array) < 2) {
                return $array;
            } else {
                $pivot = $array[0];
                $low = [];
                $high = [];
                foreach ($array as $value) {
                    if ($value < $pivot) {
                        $low[] = $value;
                    } elseif ($value > $pivot) {
                        $high[] = $value;
                    }
                }

                return array_merge(quickSort($low), [$pivot], quickSort($high));
            }
        }

        dd(quickSort($array));
        return 123;
    }

}
