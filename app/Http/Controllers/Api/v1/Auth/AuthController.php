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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/v1/login-register",
     *      operationId="login and register",
     *      tags={"Login/Register"},
     *      summary="login and register",
     *      description="login and register",
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
     *      operationId="login confirm",
     *      tags={"Login Confirm"},
     *      summary="Login Confirm",
     *      description="Login Confirm",
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
     *                  required={"otp"},
     *                  @OA\Property(property="otp", type="text", format="text", example="878787"),
     *                  @OA\Property(property="token", type="text", format="text", example="dsfjksgj")
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
        $attributes = $request->validated();
        try
        {
            $user = User::where('token', $token)->firstOrFail();
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

}
