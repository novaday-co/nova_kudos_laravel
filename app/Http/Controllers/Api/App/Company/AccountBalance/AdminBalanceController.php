<?php

namespace App\Http\Controllers\Api\App\Company\AccountBalance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Company\Balance\StatusTransactionRequest;
use App\Http\Resources\Company\Balance\TransactionUserResource;
use App\Http\Resources\User\Balance\TransactionsResource;
use App\Models\Company;
use App\Models\CompanyUserTransaction;
use Illuminate\Http\Request;

class AdminBalanceController extends Controller
{
    /**
     * @OA\Get (
     *      path="/admin/companies/{company_id}/users/transactions",
     *      operationId="get all transactions",
     *      tags={"companies"},
     *      summary="get all transactions",
     *      description="get all transactions",
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
     *          @OA\JsonContent(ref="/admin/companies/{company_id}/users/transactions")
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
    public function getTransactionUsers(Company $company_id)
    {
        try {
            $transactions= $company_id->companyUserTransactions()->latest()->paginate(10);
            return TransactionUserResource::collection($transactions);
        } catch (\Exception $exception)
        {
            return $this->error([$exception->getMessage()], '', 422);
        }
    }

    /**
     * @OA\Post  (
     *      path="/admin/companies/{company_id}/users/transactions/{transaction}/status",
     *      operationId="change transaction status",
     *      tags={"companies"},
     *      summary="change transaction status",
     *      description="change transaction status",
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
     *      name="transaction",
     *      in="path",
     *      required=true,
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
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"status"},
     *                 @OA\Property(
     *                     property="status",
     *                     type="string",
     *                     example="pending"
     *                 ),
     *             )
     *         )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/admin/companies/{company_id}/users/transactions/{transaction}/status")
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
    public function updateTransactionStatus(StatusTransactionRequest $request, Company $company_id, CompanyUserTransaction $transaction)
    {
        try {
            $attrs = $request->validated();
            $companyTransaction = $company_id->companyUserTransactions()->findOrFail($transaction->id);
            $userId = auth()->user();
            foreach ($company_id->users as $user)
            {
                $balance = $user->pivot->currency_amount;
                switch ($attrs['status']){
                    case 'done':
                    case 'pending':
                        $companyTransaction->status = $attrs['status'];
                        $balance -= $companyTransaction->amount;
                        break;
                    case 'failed':
                        $companyTransaction->status = $attrs['status'];
                        $balance += $companyTransaction->amount;
                        break;
                }
            }
            $status = $companyTransaction->save();
            $company_id->users()->updateExistingPivot($userId, array('currency_amount' => $balance));
            $company_id->companyUserTransactions()->where('id', $companyTransaction)->update(array('status' => $status));
            return TransactionUserResource::make($companyTransaction);
        } catch (\Exception $exception)
        {
            return $this->error([$exception->getMessage()], '', 422);
        }
    }
}
