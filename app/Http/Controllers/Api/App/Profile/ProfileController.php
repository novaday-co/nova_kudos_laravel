<?php

namespace App\Http\Controllers\Api\App\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Company\Profile\UpdateAvatarRequest;
use App\Http\Requests\Admin\Company\Profile\UpdateMobileRequest;
use App\Http\Requests\Auth\OtpRequest;
use App\Http\Resources\Company\User\CompanyUserResource;
use App\Http\Resources\UserResource;
use App\Models\Company;
use App\Models\TempMobile;
use App\Services\Message\MessageService;
use App\Services\Message\Sms\SmsService;
use Carbon\Carbon;

class ProfileController extends Controller
{

    /**
     * @OA\Post(
     *      path="/users/companies/{company_id}/change/avatar",
     *      operationId="change avatar user",
     *      tags={"User"},
     *      summary="change avatar user",
     *      description="change avatar user",
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
     *      @OA\Parameter(
     *      name="company_id",
     *      in="path",
     *      required=true,
     *     example=1,
     *     @OA\Schema(
     *      type="integer"
     *          )
     *      ),
     *         @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"avatar"},
     *                  @OA\Property(property="avatar", type="file", format="text"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/users/companies/{company_id}/change/avatar")
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
    public function updateProfile(UpdateAvatarRequest $request, Company $company_id)
    {
        try
        {
            $user_id = auth()->user();
            $data = $company_id->users()->findOrFail($user_id->id);
            $attrs = $request->validated();
            if ($request->hasFile('avatar')) {
                $avatar = $this->uploadImage($request->file('avatar'), 'images' . DIRECTORY_SEPARATOR . 'companies' . DIRECTORY_SEPARATOR . $company_id->id . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'avatar');
                $attrs['avatar'] = $avatar;
            }
            $company_id->users()->updateExistingPivot($user_id, array('avatar' => $attrs['avatar']));
            $user_company = $user_id->companies()->findOrFail($company_id->id);
            return CompanyUserResource::make($user_company);
        } catch (\Exception $e)
        {
            return $this->error([$e->getMessage()], trans('messages.company.profile.invalid.avatar'), 422);
        }
    }

    /**
     * @OA\Post(
     *      path="/users/change/mobile",
     *      operationId="change user mobile",
     *      tags={"User"},
     *      summary="change user mobile",
     *      description="change user mobile",
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
     *                  @OA\Property(property="mobile", type="text", format="text", example="09350000001"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/users/change/mobile")
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
    public function updateMobile(UpdateMobileRequest $request)
    {
        try {
            $attributes = $request->validated();
            $userMobile = auth()->user()->mobile;
            if ($userMobile != $attributes['mobile'])
            {
                $otpCode = mt_rand(1000, 9999);
                $tempMobile = TempMobile::query()->updateOrCreate([
                    'user_id' => auth()->user()->id,
                    'mobile' => $attributes['mobile'],
                ],[
                    'otp_code' => $otpCode,
                    'expiration_otp' => Carbon::now()->addMinutes(),
                ]);
                $smsService = new SmsService();
                $smsService->setReceptor($tempMobile->mobile);
                $smsService->setOtpCode($otpCode);
                $messageService = new MessageService($smsService);
                $messageService->send();
            } else
                return $this->error([trans('messages.profile.duplicate.mobile')], trans('messages.profile.duplicate.mobile'), 422);
            return $this->success(['otp_code' => $otpCode]);
        } catch (\Exception $e)
        {
            return $this->error([$e->getMessage()], trans('messages.profile.duplicate.mobile'), 422);
        }
    }
    /**
     * @OA\Post(
     *      path="/users/verify/mobile",
     *      operationId="verify user mobile",
     *      tags={"User"},
     *      summary="verify user mobile",
     *      description="verify user mobile",
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
     *                  @OA\Property(property="mobile", type="text", format="text", example="09350000001"),
     *                  @OA\Property(property="otp_code", type="text", format="text", example="1234"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/users/verify/mobile")
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

    public function verifyMobile(OtpRequest $request)
    {
        try {
            $attributes = $request->validated();
            $tempMobile = TempMobile::query()->where('mobile', $attributes['mobile'])
                ->where('otp_code', $attributes['otp_code'])
                ->where('expiration_otp', ">=", Carbon::now())->firstOrFail();
            $user = auth()->user();
            $user->update([
                'mobile' => $tempMobile['mobile']
            ]);
            return UserResource::make($user);
        } catch (\Exception $e)
        {
            return $this->error([$e->getMessage()], trans('messages.company.profile.invalid.verify'), 422);
        }
    }
}
