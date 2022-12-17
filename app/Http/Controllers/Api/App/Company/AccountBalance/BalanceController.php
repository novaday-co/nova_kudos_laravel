<?php

namespace App\Http\Controllers\Api\App\Company\AccountBalance;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/currencies/withdrawal/companies/{company_id}",
     *      operationId="withdrawal currency",
     *      tags={"Currency"},
     *      summary="withdrawal currency",
     *      description="withdrawal currency",
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
     *          @OA\JsonContent(ref="/api/authentication/resend-otp")
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
    public function withdrawalCurrency(Company $company_id)
    {

    }
}
