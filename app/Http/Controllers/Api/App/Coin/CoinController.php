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
     * @OA\Get (
     *      path="/admin/companies/{company_id}/coin/system",
     *      operationId="get company coin and value system",
     *      tags={"companies"},
     *      summary="get company coin and value system",
     *      description="get company coin and value system",
     *      security={ {"sanctum": {} }},
     *         @OA\Parameter(
     *          name="company_id",
     *          in="path",
     *          required=true,
     *         @OA\Schema(
     *           type="integer"
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
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/admin/companies/lists")
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
    public function getValueOfSystem(Company $company_id)
    {
        try {

            return CompanyValueResource::make($company_id->coin);
        } catch (\Exception $exception)
        {
            return $this->error(['error:' =>$exception->getMessage()], trans('messages.company.company_system_value'), 422);
        }
    }
    /**
     * @OA\Post(
     *      path="/admin/companies/{company_id}/set/coin",
     *      operationId="update coin value",
     *      tags={"companies"},
     *      summary="update coin value",
     *      description="update coin value",
     *      security={ {"sanctum": {} }},
     *      @OA\Parameter(
     *          name="company_id",
     *          in="path",
     *          required=true,
     *         @OA\Schema(
     *           type="integer"
     *          )
     *      ),
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
     *          @OA\JsonContent(ref="/api/admin/companies/{company_id}/set/coin")
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
            $coin = CoinValue::query()->updateOrCreate([
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
                CoinValue::query()->update([
                    'system_value' => $systemValue,
                ]);
            }
            $company_id->coin_value_history()->create([
                'user_id' => auth()->user()->id,
                'coin_value' => $attrs['coin_value']
            ]);
            return CoinValueResource::make($coin);
        } catch (\Exception $exception)
        {
            return $this->error(['error:' =>$exception->getMessage()], trans('messages.company.coin_value'), 422);
        }
    }
}
