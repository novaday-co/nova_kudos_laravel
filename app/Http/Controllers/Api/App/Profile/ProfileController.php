<?php

namespace App\Http\Controllers\Api\App\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Profile\ProfileRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\Image\ImageService;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{

    /**
     * @OA\Post(
     *      path="/api/app/profiles/users/companies/{company_id}",
     *      operationId="update profile user",
     *      tags={"profiles"},
     *      summary="update profile user",
     *      description="update profile user",
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
     *         @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"mobile"},
     *                  @OA\Property(property="avatar", type="file", format="text"),
     *                  @OA\Property(property="mobile", type="text", format="text", example="09124068701"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/api/app/profile/users/update/{company_id}")
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
    public function updateProfile(ProfileRequest $request, ImageService $imageService, Company $company_id)
    {
        try
        {
            $user_id = auth()->user();
            $user_company = $company_id->users()->findOrFail($user_id->id);
            $attrs = $request->validated();
            if ($request->hasFile('avatar')) {
                if (!empty($user_company->avatar))
                    $imageService->deleteImage($user_company->avatar);
                $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'profiles');
                $result = $imageService->save($request->file('avatar'));
                if ($result === false)
                    return response('error uploading photo ', 400);
                $attrs['avatar'] = $result;
            }
            $company_id->users()->updateExistingPivot($user_id, array('avatar' => $attrs['avatar']));
            return response('ok');
        } catch (\Exception $e)
        {
            return response(['bad request' => $e->getMessage()], 400);
        }
    }
}
