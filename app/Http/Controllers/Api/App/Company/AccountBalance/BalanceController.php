<?php

namespace App\Http\Controllers\Api\App\Company\AccountBalance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\Balance\BalanceRequest;
use App\Http\Resources\User\Balance\TransactionsResource;
use App\Http\Resources\User\AccountInfo\DefaultCompanyUserResource;
use App\Models\Company;

class BalanceController extends Controller
{
    /**
     * @OA\Get (
     *      path="/api/currencies/companies/{company_id}/users/transactions",
     *      operationId="get user transactions",
     *      tags={"users"},
     *      summary="get user transactions",
     *      description="get user transactions",
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
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/api/currencies/companies/{company_id}/users/transactions")
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
    public function getUserTransaction(Company $company_id)
    {
        try {
            $userId = auth()->user()->id;
            $transactions = $company_id->companyUserTransactions()->where('user_id', $userId)->latest()->paginate(10);
            return TransactionsResource::collection($transactions);
        } catch (\Exception $exception)
        {
           return $this->error([$exception->getMessage()], trans('messages.currency.transaction.invalid'), 422);
        }
    }
    /**
     * @OA\Post(
     *      path="/api/currencies/withdrawal/companies/{company_id}/users",
     *      operationId="withdrawal currency",
     *      tags={"users"},
     *      summary="withdrawal currency",
     *      description="withdrawal currency",
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
     *                     example=1200
     *                 ),
     *             )
     *         )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/api/currencies/withdrawal/companies/{company_id}/users")
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
    public function withdrawalCurrency(BalanceRequest $request, Company $company_id)
    {
        try {
            $attrs = $request->validated();
            $userId = auth()->user();
            foreach ($company_id->users as $user)
            {
                $balance = $user->pivot->currency_amount;
                if ($balance >= $attrs['amount'])
                {
                    $balance -= $attrs['amount'];
                    $company_id->users()->updateExistingPivot($userId, array('currency_amount' => $balance));
                    $company_id->companyUserTransactions()->create([
                        'user_id' => $userId->id,
                        'transaction_type' => 'withdrawal',
                        'amount' => $attrs['amount']
                    ]);
                } if ($balance < $attrs['amount'])
                {
                    return $this->error([trans('messages.currency.invalid.balance')], trans('messages.currency.invalid.balance'), 422);
                }
            }
            $userCompany = $userId->companies()->find($company_id);
                return DefaultCompanyUserResource::make($userCompany);
        } catch (\Exception $exception)
        {
            return $this->error([$exception->getMessage()], trans('messages.currency.withdrawal'), 422);
        }
    }
}
