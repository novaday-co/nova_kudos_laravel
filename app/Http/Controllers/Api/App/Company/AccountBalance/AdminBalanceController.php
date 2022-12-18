<?php

namespace App\Http\Controllers\Api\App\Company\AccountBalance;

use App\Http\Controllers\Controller;
use App\Http\Resources\Company\Balance\TransactionUserResource;
use App\Http\Resources\User\Balance\TransactionsResource;
use App\Models\Company;
use Illuminate\Http\Request;

class AdminBalanceController extends Controller
{
    /**
     * @OA\Get (
     *      path="/admin/companies/{company_id}/users/transactions",
     *      operationId="get all transactions",
     *      tags={"Currency"},
     *      summary="get all transactions",
     *      description="get all transactions",
     *      security={ {"sanctum": {} }},
     *      @OA\Parameter(
     *      name="company_id",
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

    public function updateStatus()
    {

    }
}
