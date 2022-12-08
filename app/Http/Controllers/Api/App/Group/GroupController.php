<?php

namespace App\Http\Controllers\Api\App\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Group\GroupRequest;
use App\Http\Requests\Admin\Group\UpdateGroupRequest;
use App\Http\Resources\Admin\GroupResource;
use App\Http\Services\Image\ImageService;
use App\Models\Company;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{

    /**
     * @OA\Post (
     *      path="/api/app/groups/companies/{company}",
     *      operationId="store new groups",
     *      tags={"groups"},
     *      summary="store new groups",
     *      description="store new groups",
     *      security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *          name="company",
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
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="name", type="text", format="text", example="yasin"),
     *                   required={"name"},
     *                  @OA\Property(property="avatar", type="file", format="text"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/api/app/groups/companies/{company}")
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
    public function store(GroupRequest $request, ImageService $imageService, Company $company)
    {
        try
        {
            $attrs = $request->validated();
            if ($request->hasFile('avatar')) {
                $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'groups');
                $result = $imageService->save($request->file('avatar'));
                if ($result === false)
                    return response('error uploading photo ', 400);
                $attrs['avatar'] = $result;
            }
            $attrs['company_id'] = $company->id;
            $group = Group::query()->create($attrs);
            return new GroupResource($group);
        } catch (\Exception $e)
        {
            return response(['errors: ' => $e->getMessage()], 400);
        }
        }

    /**
     * @OA\Put(
     *      path="/api/app/groups/{group}/companies/{company}",
     *      operationId="update group",
     *      tags={"groups"},
     *      summary="update group",
     *      description="update group",
     *      security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *          name="group",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="company",
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
     *                  required={"name"},
     *                  @OA\Property(property="name", type="text", format="text", example="yasin"),
     *                  @OA\Property(property="avatar", type="file", format="text"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *     @OA\JsonContent(ref="/api/app/groups/{group}/companies/{company}")
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

        public function update(UpdateGroupRequest $request, ImageService $imageService, Group $group, Company $company)
        {
            try
            {
                $attrs = $request->validated();
                if ($request->hasFile('avatar')) {
                    if (!empty($group->avatar))
                        $imageService->deleteImage($group->avatar);
                    $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'groups');
                    $result = $imageService->save($request->file('avatar'));
                    if ($result === false)
                        return response('error uploading photo ', 400);
                    $attrs['avatar'] = $result;
                }
                $attrs['company_id'] = $company->id;
                $group->update($attrs);
                return GroupResource::make($group);
            } catch (\Exception $e)
            {
                return response(['errors' => $e->getMessage()], 400);
            }
        }

    /*    public function destroy(Group $group)
        {
            try {
                DB::beginTransaction();
                $group->forceDelete();
                DB::commit();
            } catch (\Exception $e)
            {
                DB::rollBack();
                return response(['error: ' => $e->getMessage()], 400);
            }
            return response('group deleted..', 200);
        }*/

    /**
     * @OA\Post (
     *      path="/api/app/groups/{group}/companies/{company}/users/{user}",
     *      operationId="add user to group",
     *      tags={"groups"},
     *      summary="add user to group",
     *      description="add user to group",
     *      security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *          name="group",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="company",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *        @OA\Parameter(
     *          name="user",
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
     *     @OA\JsonContent(ref="/api/app/groups/{group}/users/{user}")
     *       ),
     *     @OA\Response(
     *          response=400,
     *          description="success",
     *       ),
     *     @OA\Response(
     *          response=500,
     *          description="server error",
     *      ),
     * )
     */
        public function addUser(Group $group, Company $company, User $user)
        {
            try {
                $company->users()->findOrFail([
                    'user_id' => $user->id
                ]);
                $group->users()->sync($user);
                return response('user added', 200);
            } catch (\Exception $exception) {
                return response('error', 400);
            }
       }
    }
