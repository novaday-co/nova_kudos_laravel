<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\OtpRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *      path="/authentication/login",
     *      operationId="login",
     *      tags={"Auth"},
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
     *                  @OA\Property(property="mobile", type="text", format="text", example="09350000000"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/authentication/login")
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
    public function login(LoginRequest $request)
    {
        try
        {
            $attributes = $request->validated();
           // $otpCode = mt_rand(1000, 9999);
            $otpCode = mt_rand(1000, 1000);
            $user = User::query()->where('mobile', $attributes['mobile'])->firstOrFail();
            $user->update([
               'otp_code' => $otpCode,
               'expiration_otp' => Carbon::now()->addMinutes()
            ]);
          //  $smsService = new SmsService();
          //  $smsService->setReceptor($user->mobile);
           // $smsService->setOtpCode($otpCode);
          //  $messageService = new MessageService($smsService);
          //  $messageService->send();

            return $this->success(['otp_code' => $otpCode]);
        } catch (\Exception $e)
        {
            return $this->error(['otp_code' => trans('auth.invalid.mobile')], trans('auth.invalid.mobile'), 422);
        }
    }

    /**
     * @OA\Post(
     *      path="/authentication/check-otp",
     *      operationId="check otp",
     *      tags={"Auth"},
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
     *                  required={"mobile", "otp_code"},
     *                  @OA\Property(property="mobile", type="text", format="text", example="09350000000"),
     *                  @OA\Property(property="otp_code", type="text", format="text", example="1000"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *     @OA\JsonContent(ref="/authentication/check-otp")
     *       ),
     *     @OA\Response(
     *          response=400,
     *          description="request error",
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="unauthorized",
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
           return $this->error(['otp_code' => trans('auth.invalid.code')], trans('auth.invalid.code'), 422);
        }
    }

    /**
     * @OA\Post(
     *      path="/authentication/resend-otp",
     *      operationId="resend otp code",
     *      tags={"Auth"},
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
     *                  @OA\Property(property="mobile", type="text", format="text", example="09350000000"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/authentication/resend-otp")
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
    public function resendOtp(LoginRequest $request)
    {
        try{
            $attributes = $request->validated();
            // $otpCode = mt_rand(1000, 9999);
            $otpCode = mt_rand(1000, 1000);
            $user = User::query()->where('mobile', $attributes['mobile'])
                ->where('expiration_otp', '<=', Carbon::now()->subMinutes())->firstOrFail();
            $user->update([
               'otp_code' => $otpCode,
               'expiration_otp' => Carbon::now()->addMinutes(),
            ]);
//            $smsService = new SmsService();
//            $smsService->setReceptor($user->mobile);
//            $smsService->setOtpCode($otpCode);
//            $messageService = new MessageService($smsService);
//            $messageService->send();
            return $this->success(['otp_code' => $otpCode]);
        } catch (\Exception $exception)
        {
            return $this->error(['resend_code' => trans('auth.invalid.resend')], trans('auth.invalid.resend'), 422);
        }
    }

    /**
     * @OA\Post(
     *      path="/authentication/logout",
     *      operationId="logout",
     *      tags={"Auth"},
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
     *          @OA\JsonContent(ref="/authentication/logout")
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
    public function logout(Request $request)
    {
        try {
            auth()->user()->currentAccessToken()->delete();
            return $this->success([], trans('messages.logout'));
        } catch (\Exception $exception)
        {
            return $this->error(['logout' => trans('auth.invalid.logout')], trans('auth.invalid.logout'), 422);
        }
    }
}
