<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class SaleController extends Controller
{
    public function purchase()
    {
        try {
            $sales = Sale::all();

            if ($sales->isEmpty()) {
                throw new \Exception("No sales data found", 404);
            }

            $result = [
                'result' => true,
                'data'   => $sales
            ];

        } catch(ModelNotFoundException $e) {
            $result = [
                'result' => false,
                'error'  => ['messages' => [$e->getMessage()]]
            ];
            return $this->resConversionJson($result, 404);

        } catch(\Exception $e){
            $statusCode = $e->getCode();
            if ($statusCode < 100 || $statusCode >= 600) {
                $statusCode = 500;
            }
            $result = [
                'result' => false,
                'error'  => ['messages' => [$e->getMessage()]]
            ];
            return $this->resConversionJson($result, $statusCode);
        }
        return $this->resConversionJson($result);
    }

    private function resConversionJson($result, $statusCode = 200)
    {
        return response()->json($result, $statusCode, ['Content-Type' => 'application/json'], JSON_UNESCAPED_SLASHES);
    }
}
