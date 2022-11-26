<?php

namespace App\Http\Controllers\Api\v1\Admin\Access;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Admin\Access\PermissionRequest;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * @OA\Get (
     *     path="/api/v1/admin/permissions",
     *     operationId="get all permissions",
     *     tags={"permissions"},
     *     summary="permissions",
     *     description="permissions",
     *     security={{ "apiAuth": {}},},
     *     @OA\Parameter (
     *     name="Accept",
     *     in="header",
     *     required=true,
     *     example="application/json",
     *     @OA\Schema (
     *     type="string"
     *          )
     *      ),
     *     @OA\Parameter (
     *     name="Content-Type",
     *     in="header",
     *     required=true,
     *     example="application/json",
     *     @OA\Schema (
     *     type="string"
     *          )
     *      ),
     *     @OA\Response (
     *          response=200,
     *          description="success",
     *       ),
     *     @OA\Response (
     *          response=401,
     *          description="unauthenticated",
     *       ),
     *     @OA\Response (
     *          response=403,
     *          description="forbidden",
     *       ),
     * )
     */
    public function index()
    {
        $permissions = Permission::latest()->paginate(15);
        return new PermissionResource($permissions);
    }

    /**
     * @OA\Post  (
     *     path="/api/v1/admin/permissions",
     *     operationId="store permission",
     *     tags={"store permission"},
     *     summary="store permission",
     *     description="store permission",
     *     security={{ "apiAuth": {}},},
     *     @OA\Parameter (
     *     name="Accept",
     *     in="header",
     *     required=true,
     *     example="application/json",
     *     @OA\Schema (
     *     type="string"
     *          )
     *      ),
     *     @OA\Parameter (
     *     name="Content-Type",
     *     in="header",
     *     required=true,
     *     example="application/json",
     *     @OA\Schema (
     *     type="string"
     *          )
     *      ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"name"},
     *                  @OA\Property(property="name", type="text", format="text", example="admin"),
     *               ),
     *           ),
     *       ),
     *     @OA\Response (
     *          response=200,
     *          description="success",
     *       ),
     *     @OA\Response (
     *          response=401,
     *          description="unauthenticated",
     *       ),
     *     @OA\Response (
     *          response=403,
     *          description="forbidden",
     *       ),
     * )
     */
    public function store(PermissionRequest $request)
    {
        $attr = $request->validated();
        $permission = Permission::create([
           'name' => $attr['name'],
           'guard_name' => 'web',
        ]);
        return new PermissionResource($permission);
    }

    /**
     * @OA\Put  (
     *     path="/api/v1/admin/permission/update/{permisssion}",
     *     operationId="update permission",
     *     tags={"update permission"},
     *     summary="update permission",
     *     description="update permission",
     *     security={{ "apiAuth": {}},},
     *     @OA\Parameter (
     *     name="Accept",
     *     in="header",
     *     required=true,
     *     example="application/json",
     *     @OA\Schema (
     *     type="string"
     *          )
     *      ),
     *     @OA\Parameter (
     *     name="Content-Type",
     *     in="header",
     *     required=true,
     *     example="application/json",
     *     @OA\Schema (
     *     type="string"
     *          )
     *      ),
     *     @OA\Parameter (
     *      name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema (
     *     type="integer"
     *          )
     *      ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"name"},
     *                  @OA\Property(property="name", type="text", format="text", example="admin"),
     *               ),
     *           ),
     *       ),
     *     @OA\Response (
     *          response=200,
     *          description="success",
     *       ),
     *     @OA\Response (
     *          response=401,
     *          description="unauthenticated",
     *       ),
     *     @OA\Response (
     *          response=403,
     *          description="forbidden",
     *       ),
     * )
     */
    public function update(PermissionRequest $request, Permission $permission)
    {
        $permissionUpdate = $permission->update($request->validated());
        return new PermissionResource($permissionUpdate);
    }

    /**
     * @OA\Delete  (
     *     path="/api/v1/admin/permission/delete/{permisssion}",
     *     operationId="delete permission",
     *     tags={"delete permission"},
     *     summary="delete permission",
     *     description="delete permission",
     *     security={{ "apiAuth": {}},},
     *     @OA\Parameter (
     *     name="Accept",
     *     in="header",
     *     required=true,
     *     example="application/json",
     *     @OA\Schema (
     *     type="string"
     *          )
     *      ),
     *     @OA\Parameter (
     *     name="Content-Type",
     *     in="header",
     *     required=true,
     *     example="application/json",
     *     @OA\Schema (
     *     type="string"
     *          )
     *      ),
     *      @OA\Parameter (
     *      name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema (
     *     type="integer"
     *          )
     *      ),
     *     @OA\Response (
     *          response=200,
     *          description="success",
     *       ),
     *     @OA\Response (
     *          response=401,
     *          description="unauthenticated",
     *       ),
     *     @OA\Response (
     *          response=403,
     *          description="forbidden",
     *       ),
     * )
     */
    public function destroy(Request $request, Permission $permission)
    {
        $permission->delete();
        return response([], 204);
    }

}
