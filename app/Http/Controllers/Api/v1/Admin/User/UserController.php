<?php

namespace App\Http\Controllers\Api\v1\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Admin\UpdateUserRequest;
use App\Http\Requests\v1\Admin\User\UserRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\Image\ImageService;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * @OA\Get (
     *      path="/api/v1/admin/users",
     *      operationId="get all users",
     *      tags={"admin/users"},
     *      summary="get all users",
     *      description="get all users",
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
    public function index()
    {
        $users = User::latest()->paginate(15);
        return new UserResource($users);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/admin/users",
     *      operationId="store new user",
     *      tags={"admin/users"},
     *      summary="store new user",
     *      description="store new user",
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
     *                  required={"first_name"},
     *                  @OA\Property(property="first_name", type="text", format="text", example="yasin"),
     *                  @OA\Property(property="last_name", type="text", format="text", example="baghban"),
     *                  @OA\Property(property="mobile", type="text", format="text", example="091010101010"),
     *                  @OA\Property(property="avatar", type="file", format="text"),
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
     *          response=400,
     *          description="error",
     *       ),
     *     @OA\Response(
     *          response=500,
     *          description="server error",
     *      ),
     * )
     */

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

    /**
     * @OA\Put(
     *      path="/api/v1/admin/users/{user}",
     *      operationId="update user",
     *      tags={"admin/users"},
     *      summary="update user",
     *      description="update user",
     *      security={{ "apiAuth": {}},},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
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
     *                  required={"first_name"},
     *                  @OA\Property(property="first_name", type="text", format="text", example="yasin"),
     *                  @OA\Property(property="last_name", type="text", format="text", example="baghban"),
     *                  @OA\Property(property="mobile", type="text", format="text", example="091010101010"),
     *                  @OA\Property(property="avatar", type="text", format="text"),
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
     *          response=400,
     *          description="error",
     *       ),
     *     @OA\Response(
     *          response=500,
     *          description="server error",
     *      ),
     * )
     */

    public function update(UpdateUserRequest $request, User $user, ImageService $imageService)
    {
        try
        {
            DB::beginTransaction();
            // validation
            $attrs = $request->validated();
            // check request for upload image
            if ($request->hasFile('avatar')) {
                // check image exists or not
                if (!empty($user->avatar))
                    $imageService->deleteImage($user->avatar);
                $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'users');
                $result = $imageService->save($request->file('avatar'));
                // check upload
                if ($result === false)
                    return response('error uploading photo ', 400);
                $attrs['avatar'] = $result;
            }
            // user create
            $user = User::update($attrs);
            return new UserResource($user);
        } catch (\Exception $e)
        {
            DB::rollBack();
            return response(['errors' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/admin/users/{user}",
     *      operationId="delete user",
     *      tags={"adminusers"},
     *      summary="delete user",
     *      description="delete user",
     *      security={{ "apiAuth": {}},},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
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
     *       ),
     *     @OA\Response(
     *          response=500,
     *          description="server error",
     *      ),
     * )
     */

    public function destroy(User $user)
    {
        $user->forceDelete();
        return response('user deleted', 200);
    }
}
