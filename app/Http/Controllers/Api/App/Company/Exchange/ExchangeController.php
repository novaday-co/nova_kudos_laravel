<?php

namespace App\Http\Controllers\Api\App\Company\Exchange;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Company\Exchange\ExchangeRequest;
use App\Http\Resources\Company\User\CompanyUserResource;
use App\Models\Company;
use Illuminate\Http\Request;

class ExchangeController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/exchange/currency/companies/{company_id}",
     *      operationId="exchange coin to currency",
     *      tags={"users"},
     *      summary="exchange coin to currency",
     *      description="exchange coin to currency",
     *      security={ {"sanctum": {} }},
     *      @OA\Parameter(
     *      name="company_id",
     *      in="path",
     *      required=true,
     *      example=1,
     *     @OA\Schema(
     *      type="integer"
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
     *          @OA\JsonContent(ref="/api/exchange/currency/companies/{company_id}")
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
    public function exchangeCurrency(ExchangeRequest $request, Company $company_id)
    {
        try
        {
            $attrs = $request->validated();
            $userId = auth()->user();
            $coin_value = $company_id->coin->coin_value;
            foreach ($company_id->users as $user)
            {
                $coin_balance = $user->pivot->coin_amount;
                $currency_balance = $user->pivot->currency_amount;
                if ($coin_balance >= $attrs['amount'])
                {
                    $exchange = $attrs['amount'] * $coin_value;
                    $coin_balance -= $attrs['amount'];
                    $currency_balance += $exchange;
                    $company_id->users()->updateExistingPivot($userId, array('coin_amount' => $coin_balance, 'currency_amount' => $currency_balance));
                    $company_id->companyUserTransactions()->create([
                        'user_id' => $userId->id,
                        'transaction_type' => 'ToCurrency',
                        'amount' => $exchange
                    ]);
                } if ($coin_balance < $attrs['amount'])
                    return $this->error([trans('messages.currency.invalid.balance')],trans('messages.currency.invalid.balance'), 422);
            }
            $userCompany = $userId->companies()->findOrFail($company_id->id);
            return CompanyUserResource::make($userCompany);
        } catch (\Exception $exception)
        {
            return $this->error([$exception->getMessage()], trans('messages.currency.invalid.exchange'), 422);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/users/exchange/coin/companies/{company_id}",
     *      operationId="exchange currency to coin",
     *      tags={"users"},
     *      summary="exchange currency to coin",
     *      description="exchange currency to coin",
     *      security={ {"sanctum": {} }},
     *      @OA\Parameter(
     *      name="company_id",
     *      in="path",
     *      required=true,
     *      example=1,
     *     @OA\Schema(
     *      type="integer"
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
     *          @OA\JsonContent(ref="/api/users/exchange/coin/companies/{company_id}")
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
    public function exchangeCoin(ExchangeRequest $request, Company $company_id)
    {
        try
        {
            $attrs = $request->validated();
            $userId = auth()->user();
            $coin_value = $company_id->coin->coin_value;
            foreach ($company_id->users as $user)
            {
                $coin_balance = $user->pivot->coin_amount;
                $currency_balance = $user->pivot->currency_amount;
                if ($currency_balance >= $attrs['amount'])
                {
                    if ($coin_value > $attrs['amount'])
                        return $this->error([trans('messages.currency.invalid.balance')], trans('messages.currency.invalid.balance'), 422);
                    $exchange = $attrs['amount'] / $coin_value;
                    $final = $attrs['amount'] - $coin_value;
                    $currency_balance -= $attrs['amount'];
                    // $currency_balance += $final;
                    $coin_balance += round($exchange);
                    $company_id->users()->updateExistingPivot($userId, array('coin_amount' => $coin_balance, 'currency_amount' => $currency_balance));
                    $company_id->companyUserTransactions()->create([
                        'user_id' => $userId->id,
                        'transaction_type' => 'ToCoin',
                        'amount' => $exchange
                    ]);
                } if ($currency_balance < $attrs['amount'])
                    return $this->error([trans('messages.currency.invalid.balance')],trans('messages.currency.invalid.balance'), 422);
            }
            $userCompany = $userId->companies()->findOrFail($company_id->id);
            return CompanyUserResource::make($userCompany);
        } catch (\Exception $exception)
        {
            return $this->error([$exception->getMessage()], trans('messages.currency.invalid.exchange'), 422);
        }
    }
}
