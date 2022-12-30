<?php

namespace App\Http\Controllers\Api\App\Company\AccountInfo;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\AccountInfo\CompanyInfoResource;
use Illuminate\Http\Request;

class AccountInfoController extends Controller
{
    /**
     * @OA\Get (
     *      path="/api/account/user/default-company",
     *      operationId="get default company",
     *      tags={"account info"},
     *      summary="get default company",
     *      description="get default company",
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
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/api/account/user/default-company")
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

    public function defaultCompany()
    {
        $user = auth()->user();
        return CompanyInfoResource::make($user);
    }
}
