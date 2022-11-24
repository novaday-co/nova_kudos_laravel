<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Auth\LoginRegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\Message\MessageService;
use App\Http\Services\Message\Sms\SmsService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginRegisterController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/v1/register",
     *      operationId="store new Reigster",
     *      tags={"Register"},
     *      summary="register new user",
     *      description="register new user",
     *      security={{ "apiAuth": {}},},
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
     *                  required={"mobile"},
     *                  @OA\Property(property="mobile", type="text", format="text", example="09124068701"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=201,
     *          description="create success",
     *       ),
     *     @OA\Response(
     *          response=422,
     *          description="error",
     *       ),
     * )
     */

    public function register(LoginRegisterRequest $request)
    {
        $attributes = $request->validated();
        try
        {
            $otpCode = mt_rand(100000, 999999);
            $token =Hash::make('SDFAGSfgjskdgj@cfgfh!!!kj&&');
            $user = User::create([
                'mobile' => $request->mobile,
                'otp_code' => $otpCode,
                'token' => $token,
            ]);
            $smsService = new SmsService();
            $smsService->setReceptor($request->mobile);
            $smsService->setMessage("otp_code :  $otpCode");
            $messageService = new MessageService($smsService);
            $messageService->send();
            return new UserResource($user);
        } catch (\Exception $e)
        {
            return response(['errors' => $e->getMessage()], 422);
        }
    }
}
