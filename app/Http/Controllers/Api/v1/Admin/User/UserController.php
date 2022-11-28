<?php

namespace App\Http\Controllers\Api\v1\Admin\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Admin\User\UpdateUserRequest;
use App\Http\Requests\v1\Admin\User\UserRequest;
use App\Http\Resources\Admin\UserResource;
use App\Http\Services\Image\ImageService;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * @OA\Get (
     *      path="/api/v1/admin/users/all",
     *      operationId="get all users",
     *      tags={"users"},
     *      summary="get all users",
     *      description="get all users",
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
     *      tags={"users"},
     *      summary="store new user",
     *      description="store new user",
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
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="first_name", type="text", format="text", example="yasin"),
     *                  @OA\Property(property="last_name", type="text", format="text", example="baghban"),
     *                   required={"mobile"},
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
     *      tags={"users"},
     *      summary="update user",
     *      description="update user",
     *      security={ {"sanctum": {} }},
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
            // user update
            $user = $user->update($attrs);
            DB::commit();
        } catch (\Exception $e)
        {
            DB::rollBack();
            return response(['errors' => $e->getMessage()], 400);
        }
        return new UserResource($user);
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/admin/users/{user}",
     *      operationId="delete user",
     *      tags={"users"},
     *      summary="delete user",
     *      description="delete user",
     *      security={ {"sanctum": {} }},
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
        try {
            DB::beginTransaction();
            $user->forceDelete();
            DB::commit();
        } catch (\Exception $e)
        {
            DB::rollBack();
            return response(['error: ' => $e->getMessage()], 400);
        }
        return response('group deleted..', 200);
    }
}
