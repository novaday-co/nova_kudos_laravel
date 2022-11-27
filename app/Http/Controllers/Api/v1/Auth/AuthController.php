<?php

namespace App\Http\Controllers\Api\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Auth\LoginRegisterRequest;
use App\Http\Requests\v1\Auth\OtpRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\Message\MessageService;
use App\Http\Services\Message\Sms\SmsService;
use App\Models\User;
use Carbon\Carbon;

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

            // generation
            $otpCode = mt_rand(100000, 999999);

            // update or create user
            $user = User::updateOrCreate([
                'mobile' => $attributes['mobile'],
            ],[
                'otp_code' => $otpCode,
                'expiration_otp' => Carbon::now()->addMinutes(10),
            ]);

            // send sms
            $smsService = new SmsService();
            $smsService->setReceptor($user->mobile);
            $smsService->setOtpCode($otpCode);
            $messageService = new MessageService($smsService);
            $messageService->send();

            return response(['message' => 'otp code sent..'], 200);
        } catch (\Exception $e)
        {
            return response(['errors' => $e->getMessage()], 422);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/v1/check-otp",
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
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="mobile", type="text", format="text", example="09354069701"),
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

    public function checkOtp(OtpRequest $request)
    {
       try
       {
           // validation
           $attributes = $request->validated();
          $user = User::where('mobile', $attributes['mobile'])->where('otp_code', $attributes['otp_code'])
              ->where('expiration_otp', ">=", Carbon::now())->firstOrFail();
                $user->update(['activation_date' => Carbon::now()]);
                $user->token =  $user->createToken('api token')->plainTextToken;
                return new UserResource($user);
         } catch (\Exception $e)
        {
           return response(['errors' => $e->getMessage()], 500);
        }
    }
}
