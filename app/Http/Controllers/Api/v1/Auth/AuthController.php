<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Auth\LoginRegisterRequest;
use App\Http\Requests\v1\Auth\OtpRequest;
use App\Http\Services\Message\MessageService;
use App\Http\Services\Message\Sms\SmsService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/v1/login-register",
     *      operationId="login and register",
     *      tags={"login register"},
     *      summary="login register",
     *      description="login register",
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
     *          response=200,
     *          description="success",
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

    public function login(LoginRegisterRequest $request)
    {
        try
        {
            // validation
            $attributes = $request->validated();
            // find user
            $user = User::where('mobile', $attributes['mobile'])->first();
            // generation
            $otpCode = mt_rand(100000, 999999);
            $token = Str::random(15);
            // login user
            if ($user) {
                $user->update([
                   'otp_code' => $otpCode,
                    'token' => $token,
                ]);
            } else {
                // register user
                $user = User::create([
                    'mobile' => $attributes['mobile'],
                    'otp_code' => $otpCode,
                    'token' => $token,
                ]);
            }
            // send sms
            $smsService = new SmsService();
            $smsService->setReceptor($user->mobile);
            $smsService->setMessage("Login Code :  $otpCode");
            $messageService = new MessageService($smsService);
            $messageService->send();

            return response(['token' => $token], 200);
        } catch (\Exception $e)
        {
            return response(['errors' => $e->getMessage()], 422);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/check-otp/{token}",
     *      operationId="check otp",
     *      tags={"check otp"},
     *      summary="check otp",
     *      description="check otp",
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
     *     @OA\Parameter(
     *     name="token",
     *     description="token login",
     *     required=true,
     *     in="path",
     *     @OA\Schema(
     *     type="string"
     *        )
     *     ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"otp"},
     *                  @OA\Property(property="otp", type="text", format="text", example="878787"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
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

    public function checkOtp(OtpRequest $request, $token)
    {
        // validation
        $attributes = $request->validated();
        try
        {
            $user = User::where('token', $token)->firstOrFail();
            // otp check
            if ($user->otp_code == $attributes['otp_code'])
            {
                $user->update(['activation_date' => Carbon::now()]);
                Auth::login($user);
                return response(['message' => 'login success']);
            }
        } catch (\Exception $e)
        {
            return response(['errors' => $e->getMessage()], 422);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/resend-otp/{token}",
     *      operationId="resend otp",
     *      tags={"resend otp"},
     *      summary="resend otp",
     *      description="resend otp",
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
     *     @OA\Parameter(
     *     name="token",
     *     description="token login",
     *     required=true,
     *     in="path",
     *     @OA\Schema(
     *     type="string"
     *        )
     *     ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"otp"},
     *                  @OA\Property(property="otp", type="text", format="text", example="878787"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
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

    public function resendOtp($token)
    {
        try
        {
            $user = User::where('token', $token)->firstOrFail();
            $otpCode = mt_rand(100000, 999999);
            $new_token = Str::random(15);
            $user->update([
                'otp_code' => $otpCode,
                'token' => $new_token,
            ]);
            // send sms
            $smsService = new SmsService();
            $smsService->setReceptor($user->mobile);
            $smsService->setMessage("Login Code :  $otpCode");
            $messageService = new MessageService($smsService);
            $messageService->send();
        } catch (\Exception $e)
        {
            return response(['errors' => $e->getMessage()], 422);
        }
    }

}
