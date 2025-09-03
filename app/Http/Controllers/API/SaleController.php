<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    public function purchase(Request $request): JsonResponse
    {
        try {
            $productId = $request->input('product_id');
            $quantity  = $request->input('quantity');

            $product = Product::find($productId);

            if (!$product) {
                return $this->resConversionJson([
                    'result' => false,
                    'error'  => ['messages' => ['商品が見つかりません']]
                ], 404);
            }

            if ($product->stock < $quantity) {
                return $this->resConversionJson([
                    'result' => false,
                    'error'  => ['messages' => ['在庫が不足しています']]
                ], 400);
            }

            DB::transaction(function () use ($product, $quantity) {
                // 在庫を減らす
                $product->decrement('stock', $quantity);

                // sales に記録
                Sale::create([
                    'product_id'   => $product->id,
                    'quantity'     => $quantity,
                    'price'        => $product->price * $quantity,
                    'purchased_at' => now(),
                ]);
            });

            $result = [
                'result' => true,
                'message' => '購入処理が完了しました'
            ];

            return $this->resConversionJson($result);

        } catch (\Exception $e) {
            $statusCode = $this->determineStatusCode($e);

            $result = [
                'result' => false,
                'error'  => ['messages' => [$e->getMessage()]]
            ];
            return $this->resConversionJson($result, $statusCode);
        }
    }

    private function resConversionJson(array $result, int $statusCode = 200): JsonResponse
    {
        return response()->json($result, $statusCode, ['Content-Type' => 'application/json'], JSON_UNESCAPED_SLASHES);
    }

    private function determineStatusCode(\Exception $e)
    {
        $code = $e->getCode();

        // 数値ならそのまま、数値でなければ 500
        return is_numeric($code) ? (int)$code : 500;
    }
}