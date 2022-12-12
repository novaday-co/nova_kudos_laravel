<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\OtpRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\Message\MessageService;
use App\Http\Services\Message\Sms\SmsService;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/authentication/login",
     *      operationId="login",
     *      tags={"Login"},
     *      summary="login",
     *      description="login",
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
     *          @OA\JsonContent(ref="/api/authentication/login-register")
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
    public function login(LoginRequest $request)
    {
        try
        {
            $attributes = $request->validated();
            $otpCode = mt_rand(100000, 999999);
            $user = User::query()->where('mobile', $attributes['mobile'])->firstOrFail();
            $user->update([
               'otp_code' => $otpCode,
               'expiration_otp' => Carbon::now()->addMinutes()
            ]);
            $smsService = new SmsService();
            $smsService->setReceptor($user->mobile);
            $smsService->setOtpCode($otpCode);
            $messageService = new MessageService($smsService);
            $messageService->send();

            return response([trans('messages.sent_otp') => $otpCode], 200);
        } catch (\Exception $e)
        {
            return response(trans('auth.invalid_mobile'), 400);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/authentication/check-otp",
     *      operationId="check otp",
     *      tags={"Login"},
     *      summary="check otp",
     *      description="check otp",
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
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"mobile"},
     *                  @OA\Property(property="mobile", type="text", format="text", example="09124068701"),
     *                  required={"otp_code"},
     *                  @OA\Property(property="otp_code", type="text", format="text", example="091234"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *     @OA\JsonContent(ref="/api/authentication/check-otp")
     *       ),
     *     @OA\Response(
     *          response=400,
     *          description="request error",
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="validation error",
     *       ),
     *     @OA\Response(
     *          response=500,
     *          description="server error",
     *      ),
     * )
     */
    public function checkOtp(OtpRequest $request)
    {
       try
       {
           $attributes = $request->validated();
            $user = User::query()->where('mobile', $attributes['mobile'])->where('otp_code', $attributes['otp_code'])
              ->where('expiration_otp', ">=", Carbon::now())->firstOrFail();
                $user->update(['activation_date' => Carbon::now(), 'login_count' => $user['login_count'] + 1]);
                $user->token =  $user->createToken('api token')->plainTextToken;
                return UserResource::make($user, $user->token);
         } catch (\Exception $e)
        {
           return response(trans('auth.invalid_code'), 400);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/authentication/resend-otp",
     *      operationId="resend otp code",
     *      tags={"Login"},
     *      summary="resend otp code",
     *      description="resend otp code",
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
     *          @OA\JsonContent(ref="/api/authentication/resend-otp")
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
    public function resendOtp(LoginRequest $request)
    {
        try{
            $attributes = $request->validated();
            $otpCode = mt_rand(100000, 999999);
            $user = User::query()->where('mobile', $attributes['mobile'])
                ->where('expiration_otp', '<=', Carbon::now()->subMinutes())->firstOrFail();
            $user->update([
               'otp_code' => $otpCode,
               'expiration_otp' => Carbon::now()->addMinutes(),
            ]);
            $smsService = new SmsService();
            $smsService->setReceptor($user->mobile);
            $smsService->setOtpCode($otpCode);
            $messageService = new MessageService($smsService);
            $messageService->send();
            return response([trans('messages.sent_otp') => $otpCode], 200);
        } catch (\Exception $exception)
        {
            return response(trans('auth.invalid_resend'), 400);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/authentication/logout",
     *      operationId="logout",
     *      tags={"Login"},
     *      summary="logout",
     *      description="logout",
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
     *          @OA\JsonContent(ref="/api/authentication/logout")
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
    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();
        return response(trans('messages.logout'), 200);
    }
}
