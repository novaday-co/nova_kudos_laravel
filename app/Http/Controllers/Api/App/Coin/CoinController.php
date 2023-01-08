<?php

namespace App\Http\Controllers\Api\App\Coin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Coin\CoinRequest;
use App\Http\Resources\Company\Coin\CoinValueResource;
use App\Http\Resources\Company\Coin\CompanyValueResource;
use App\Models\CoinValue;
use App\Models\Company;


class CoinController extends Controller
{
    /**
     * @OA\Post(
     *      path="/companies/{company_id}/set/coin",
     *      operationId="update coin value",
     *      tags={"Company"},
     *      summary="update coin value",
     *      description="update coin value",
     *      security={ {"sanctum": {} }},
     *      @OA\Parameter(
     *          name="locale",
     *          in="header",
     *          required=true,
     *          example="fa",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
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
     *         @OA\Parameter(
     *          name="company_id",
     *          in="path",
     *          required=true,
     *          example=1,
     *         @OA\Schema(
     *           type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"coin_value"},
     *                  @OA\Property(property="coin_value", type="text", format="text", example="1234"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/companies/{company_id}/set/coin")
     *       ),
     *     @OA\Response(
     *          response=401,
     *          description="unauthorized",
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
    public function updateValue(CoinRequest $request, Company $company_id)
    {
        try {
            $attrs = $request->validated();
            CoinValue::query()->updateOrCreate([
                'company_id' => $company_id->id,
            ],[
                'coin_value' => $attrs['coin_value'],
            ]);
            $coin_value = $company_id->coin;
            foreach ($company_id->users as $user)
            {
                $countOfCoin = $user->pivot->sum('coin_amount');
                $valueOfCurrency = $user->pivot->sum('currency_amount');
                $systemValue = $countOfCoin * $coin_value->coin_value + $valueOfCurrency;
                $company_id->coin()->update([
                    'system_value' => $systemValue
                ]);
            }
            $company_id->coin_value_history()->create([
                'user_id' => auth()->user()->id,
                'coin_value' => $attrs['coin_value']
            ]);
            $values = $company_id->coin()->first();
            return CoinValueResource::make($values);
        } catch (\Exception $exception)
        {
            return $this->error([$exception->getMessage()], trans('messages.company.coin_value'), 422);
        }
    }
}
