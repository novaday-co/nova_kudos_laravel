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
     *      path="/users/companies/{company_id}/products/{product_id}",
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
     *          name="company_id",
     *          in="path",
     *          required=true,
     *          example=1,
     *          @OA\Schema(
     *              type="integer"
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
     *          @OA\JsonContent(ref="/users/companies/{company_id}/products/{product_id}")
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
    public function addProduct(Company $company_id, Product $product_id)
    {
        DB::beginTransaction();
        $user = $company_id->users()->where('user_id', auth()->id())->firstOrFail();
        $product = $company_id->products()->where('id', $product_id->id)->firstOrFail();
        $user_balance = $user->pivot->coin_amount;
        if ($user_balance >= $product->coin)
        {
            if ($product->amount > 0)
            {
                $user_balance -= $product->coin;
                $tracking_code = Str::random(10);
                $company_id->users()->updateExistingPivot($user, array('coin_amount' => $user_balance));
                $product->update(array('amount' => $product_id->amount - 1));
               $trans = $company_id->companyUserProductTransaction()->create([
                    'user_id' => auth()->id(),
                    'product_id' => $product->id,
                    'tracking_code' => $tracking_code
                ]);
             //   $userUpdate = $company_id->users()->where('user_id', auth()->id())->firstOrFail();
           //     $user->load(['companies' => function($query) use ($user){
               //     $query->whereIn('user_id', [$user->id]);
               // }]);
               // $user->load(['companyUserProductTransactions' => function($query) use ($user){
                 //   $query->whereIn('user_id', [$user->id]);
               // }]);
                DB::commit();
                return $this->success([AddProductResource::make($trans)], trans('messages.company.market.success.buy'));
            }
            return $this->error([trans('messages.company.market.invalid.amount')], trans('messages.company.market.invalid.amount'), 500);
        } else {
            DB::rollBack();
            return $this->error([trans('messages.currency.invalid.balance')], trans('messages.currency.invalid.balance'), 500);
        }
    }
}
