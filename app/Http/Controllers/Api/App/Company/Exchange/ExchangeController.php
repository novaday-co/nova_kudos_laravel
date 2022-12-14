<?php

namespace App\Http\Controllers\Api\App\Company\Exchange;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Company\Exchange\ExchangeRequest;
use App\Http\Resources\Company\Exchange\ExchangeResource;
use App\Http\Resources\Company\User\CompanyUserResource;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExchangeController extends Controller
{
    /**
     * @OA\Post(
     *      path="/users/exchange-currency",
     *      operationId="exchange coin to currency",
     *      tags={"User"},
     *      summary="exchange coin to currency",
     *      description="exchange coin to currency",
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
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"amount"},
     *                 @OA\Property(
     *                     property="amount",
     *                     type="integer",
     *                     example=12,
     *                 ),
     *             )
     *         )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/users/companies/{company_id}/exchange/currency")
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
    public function exchangeCurrency(ExchangeRequest $request)
    {
        try
        {
            DB::beginTransaction();
            $attrs = $request->validated();
            $userId = auth()->user();
            $company_user = $userId->companies()->where('company_id', $userId->default_company)->firstOrFail();
            $coin_value = $company_user->coin->coin_value;
            $coin_balance = $company_user->pivot->coin_amount;
            $currency_balance = $company_user->pivot->currency_amount;
            if ($coin_balance >= $attrs['amount'])
            {
                $exchange = $attrs['amount'] * $coin_value;
                $coin_balance -= $attrs['amount'];
                $currency_balance += $exchange;
                $company_user->users()->updateExistingPivot($userId, array('coin_amount' => $coin_balance, 'currency_amount' => $currency_balance));
                $company_user->companyUserTransactions()->create([
                    'user_id' => $userId->id,
                    'transaction_type' => 'ToCurrency',
                    'amount' => $exchange
                ]);
            } if ($coin_balance < $attrs['amount'])
            {
                return $this->error([trans('messages.currency.invalid.balance')],trans('messages.currency.invalid.balance'), 422);
            }
            $userCompany = $userId->companies()->where('company_id', $userId->default_company)->firstOrFail();
            DB::commit();
            return ExchangeResource::make($userCompany);
        } catch (\Exception $exception)
        {
            DB::rollBack();
            return $this->error([$exception->getMessage()], trans('messages.currency.invalid.exchange'), 422);
        }
    }

    /**
     * @OA\Post(
     *      path="/users/exchange-coin",
     *      operationId="exchange currency to coin",
     *      tags={"User"},
     *      summary="exchange currency to coin",
     *      description="exchange currency to coin",
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
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"amount"},
     *                 @OA\Property(
     *                     property="amount",
     *                     type="integer",
     *                     example=12000,
     *                 ),
     *             )
     *         )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/users/exchange-coin")
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
    public function exchangeCoin(ExchangeRequest $request)
    {
        try
        {
            DB::beginTransaction();
            $attrs = $request->validated();
            $userId = auth()->user();
            $company_user = $userId->companies()->where('company_id', $userId->default_company)->firstOrFail();
            $coin_value = $company_user->coin->coin_value;
            $coin_balance = $company_user->pivot->coin_amount;
            $currency_balance = $company_user->pivot->currency_amount;
            if ($currency_balance >= $attrs['amount'])
            {
                if ($coin_value > $attrs['amount'])
                    return $this->error([trans('messages.currency.invalid.coin_value')], trans('messages.currency.invalid.coin_value'), 422);
                $exchange = $attrs['amount'] / $coin_value;
                $additionalAmount = $attrs['amount'] - $coin_value * $exchange;
                $currency_balance -= $attrs['amount'];
                $currency_balance += $additionalAmount;
                $coin_balance += round($exchange);
                $company_user->users()->updateExistingPivot($userId, array('coin_amount' => $coin_balance, 'currency_amount' => $currency_balance));
                $company_user->companyUserTransactions()->create([
                    'user_id' => $userId->id,
                    'transaction_type' => 'ToCoin',
                    'amount' => $exchange
                ]);
            } if ($currency_balance < $attrs['amount'])
                    {
                        return $this->error([trans('messages.currency.invalid.balance')],trans('messages.currency.invalid.balance'), 422);
                    }
            $userCompany = $userId->companies()->where('company_id', $userId->default_company)->firstOrFail();
            DB::commit();
            return ExchangeResource::make($userCompany);

        } catch (\Exception $exception)
        {
            DB::rollBack();
            return $this->error([$exception->getMessage()], trans('messages.currency.invalid.exchange'), 422);
        }
    }
}
