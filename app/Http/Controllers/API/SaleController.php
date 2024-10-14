<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;

class SaleController extends Controller
{

    public function purchase(): JsonResponse
    {
        try {
            $sales = Sale::all();

            if ($sales->isEmpty()) {
                return $this->resConversionJson(
                    [
                        'result' => false,
                        'error'  => ['messages' => ["販売データが見つかりません"]]
                    ],
                    404
                );
            }

            $result = [
                'result' => true,
                'data'   => $sales
            ];

        } catch(\Exception $e) {
            $statusCode = $this->determineStatusCode($e);

            $result = [
                'result' => false,
                'error'  => ['messages' => [$e->getMessage()]]
            ];
            return $this->resConversionJson($result, $statusCode);
        }
        return $this->resConversionJson($result);
    }

    private function resConversionJson(array $result, int $statusCode = 200): JsonResponse
    {
        return response()->json($result, $statusCode, ['Content-Type' => 'application/json'], JSON_UNESCAPED_SLASHES);
    }

    private function determineStatusCode(\Exception $e): int
    {
        $statusCode = $e->getCode();
        return ($statusCode < 100 || $statusCode >= 600) ? 500 : $statusCode;
    }
}
