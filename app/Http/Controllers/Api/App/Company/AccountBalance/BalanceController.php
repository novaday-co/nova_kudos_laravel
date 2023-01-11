<?php

namespace App\Http\Controllers\Api\App\Company\AccountBalance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\Balance\BalanceRequest;
use App\Http\Resources\Company\Withdrawal\WithDrawalResource;
use App\Http\Resources\User\Balance\TransactionsResource;
use App\Http\Resources\User\AccountInfo\DefaultCompanyUserResource;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class BalanceController extends Controller
{
    /**
     * @OA\Get (
     *      path="/users/transaction-currencies",
     *      operationId="get user transactions",
     *      tags={"User"},
     *      summary="get user transactions",
     *      description="get user transactions",
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
     *      @OA\Parameter(
     *          name="page",
     *          in="query",
     *          required=false,
     *          example=2,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="query_count",
     *          in="query",
     *          required=false,
     *          example=5,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/users/transaction-currencies")
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
    public function getUserTransaction(Request $request)
    {
        try {
            $userId = auth()->user();
            $company_user = $userId->companies()->where('company_id', $userId->default_company)->firstOrFail();
            if ($request->has('page'))
            {
                $currentPage = $request->page;
                Paginator::currentPageResolver(function () use ($currentPage) {
                    return $currentPage;
                });
            }
            if ($request->has('query_count')) {
                $transactions = $company_user->companyUserTransactions()->latest()->paginate((int) $request->query_count);
                return TransactionsResource::collection($transactions);
            }
            $transactions = $company_user->companyUserTransactions()->latest()->paginate(10);
            return TransactionsResource::collection($transactions);
        } catch (\Exception $exception)
        {
           return $this->error([$exception->getMessage()], trans('messages.currency.transaction.invalid'), 422);
        }
    }
    /**
     * @OA\Post(
     *      path="/users/withdrawal-currency",
     *      operationId="withdrawal currency",
     *      tags={"User"},
     *      summary="withdrawal currency",
     *      description="withdrawal currency",
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
     *                     example=1200
     *                 ),
     *             )
     *         )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/users/withdrawal-currency")
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
    public function withdrawalCurrency(BalanceRequest $request)
    {
        try {
            DB::beginTransaction();
            $attrs = $request->validated();
            $userId = auth()->user();
            $company_user = $userId->companies()->where('company_id', $userId->default_company)->firstOrFail();
            $balance = $company_user->pivot->currency_amount;
            $this->checkMax($company_user->max_withdrawal, $attrs['amount']);
            if ($company_user->withdrawal_permission !== 'enable')
            {
                return $this->error([trans('messages.company.setting.permission.invalid')], trans('messages.company.setting.permission.invalid'), 500);
            }
            if ($company_user->min_withdrawal > $attrs['amount'])
            {
                return $this->error([trans('messages.company.setting.min.invalid')], trans('messages.company.setting.min.invalid'), 500);
            }
            if ($balance >= $attrs['amount'])
            {
                $balance -= $attrs['amount'];
                $company_user->users()->updateExistingPivot($userId, array('currency_amount' => $balance));
                $company_user->companyUserTransactions()->create([
                    'user_id' => $userId->id,
                    'transaction_type' => 'withdrawal',
                    'amount' => $attrs['amount']
                ]);
                $userCompany = $userId->companies()->where('company_id', $userId->default_company)->firstOrFail();
                DB::commit();
                return WithDrawalResource::make($userCompany);
            } else if ($balance < $attrs['amount'])
            {
                    DB::rollBack();
                return $this->error([trans('messages.currency.invalid.balance')], trans('messages.currency.invalid.balance'), 422);
            }
        } catch (\Exception $exception)
        {
            return $this->error([$exception->getMessage()], trans('messages.currency.withdrawal'), 422);
        }
    }

    private function checkMax($max, $request)
    {
        try {
            if ($max >= $request)
            {
                return true;
            }
        } catch (\Exception $exception)
        {
            return $this->error(['max_withdrawal' => trans('messages.company.max.invalid')], trans('messages.company.max.invalid'), 500);
        }
    }
}
