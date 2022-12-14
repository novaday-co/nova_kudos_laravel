<?php

namespace App\Http\Controllers\Api\App\Company\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Company\Product\UserProductRequest;
use App\Http\Resources\Company\product\AddProductResource;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserProductController extends Controller
{

    /**
     * @OA\Post   (
     *      path="/users/products/{product_id}",
     *      operationId="buy  product",
     *      tags={"User"},
     *      summary="buy product",
     *      description="buy product",
     *      security={ {"sanctum": {} }},
     *      @OA\Parameter(
     *          name="Accept",
     *          in="header",
     *          required=true,
     *          example="application/json",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="Content-Type",
     *          in="header",
     *          required=true,
     *          example="application/json",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="product_id",
     *          in="path",
     *          required=true,
     *          example=1,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/users/products/{product_id}")
     *       ),
     *     @OA\Response(
     *          response=401,
     *          description="validation error",
     *      ),
     *     @OA\Response(
     *          response=422,
     *          description="error",
     *       ),
     *     @OA\Response(
     *          response=500,
     *          description="server error",
     *      ),
     * )
     */
    public function addProduct(Product $product_id)
    {
        DB::beginTransaction();
        $userId = auth()->user();
        $company_user = $userId->companies()->where('company_id', $userId->default_company)->firstOrFail();
        $product = $company_user->products()->where('id', $product_id->id)->firstOrFail();
        $user_balance = $company_user->pivot->coin_amount;
        if ($user_balance >= $product->coin)
        {
            if ($product->amount > 0)
            {
                $user_balance -= $product->coin;
                $tracking_code = Str::random(10);
                $company_user->users()->updateExistingPivot($userId->id, array('coin_amount' => $user_balance));
                $product->update(array('amount' => $product_id->amount - 1));
               $trans = $company_user->companyUserProductTransaction()->create([
                    'user_id' => auth()->id(),
                    'product_id' => $product->id,
                    'tracking_code' => $tracking_code
                ]);
                DB::commit();
                return new AddProductResource($trans);
            }
            return $this->error(['product_amount' => trans('messages.company.market.invalid.amount')], trans('messages.company.market.invalid.amount'), 500);
        } else {
            DB::rollBack();
            return $this->error(['coin_amount' => trans('messages.currency.invalid.balance')], trans('messages.currency.invalid.balance'), 500);
        }
    }
}
