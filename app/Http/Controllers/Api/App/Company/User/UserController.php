<?php

namespace App\Http\Controllers\Api\App\Company\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Http\Requests\Admin\User\UserRequest;
use App\Http\Resources\Company\User\CompanyUserResource;
use App\Models\Company;
use App\Models\User;
use App\Services\Image\ImageService;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * @OA\Get (
     *      path="/users/companies/{company_id}/members",
     *      operationId="get all users",
     *      tags={"User"},
     *      summary="get all users",
     *      description="get all users company",
     *      security={ {"sanctum": {} }},
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
     *          name="company_id",
     *          in="path",
     *          required=true,
     *          example=1,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      @OA\JsonContent(ref="/users/companies/{company_id}/members")
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
    public function getAllUser(Company $company_id)
    {
        $users = $company_id->users()->latest()->paginate(10);
        return CompanyUserResource::collection($users);
    }


    public function store(UserRequest $request, ImageService $imageService)
    {
        try
        {
           DB::beginTransaction();
           // validation
            $attrs = $request->validated();
            // check request for upload image
            if ($request->hasFile('avatar')) {
                $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'users');
                $result = $imageService->save($request->file('avatar'));
                // check upload
                if ($result === false)
                    return response('error uploading photo ', 400);
                $attrs['avatar'] = $result;
            }
            //create user
           $user = User::create($attrs);
           DB::commit();
        } catch (\Exception $e)
        {
            DB::rollBack();
            return response(['errors: ' => $e->getMessage()], 400);
        }
        return new UserResource($user);
    }


    public function update(UpdateUserRequest $request, User $user, ImageService $imageService)
    {
        try
        {
            DB::beginTransaction();
            $attrs = $request->validated();
            if ($request->hasFile('avatar')) {
                if (!empty($user->avatar))
                    $imageService->deleteImage($user->avatar);
                $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'users');
                $result = $imageService->save($request->file('avatar'));
                if ($result === false)
                    return response('error uploading photo ', 400);
                $attrs['avatar'] = $result;
            }
            $user = $user->update($attrs);
            DB::commit();
        } catch (\Exception $e)
        {
            DB::rollBack();
            return response(['errors' => $e->getMessage()], 400);
        }
        return new UserResource($user);
    }


    public function destroy(User $user)
    {
//        try {
//            DB::beginTransaction();
//            $user->forceDelete();
//            DB::commit();
//        } catch (\Exception $e)
//        {
//            DB::rollBack();
//            return response(['error: ' => $e->getMessage()], 400);
//        }
//        return response('group deleted..', 200);
    }
}
