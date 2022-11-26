<?php

namespace App\Http\Controllers\Api\v1\Admin\Access;

use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Admin\Access\RoleRequest;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * @OA\Get (
     *     path="/api/v1/admin/roles",
     *     operationId="get all roles",
     *     tags={"roles"},
     *     summary="roles",
     *     description="roles",
     *     security={{ "apiAuth": {}},},
     *     @OA\Parameter (
     *      name='"Accept",
     *     in="header",
     *     required=true,
     *     example="application/json",
     *     @OA\Schema (
     *     type="string"
     *         )
     *    ),
     *     @OA\Parameter (
     *     name="Content-Type",
     *     in="header",
     *     required=true,
     *     example="application/json",
     *     @OA\Schema (
     *     type="string"
     *         )
     *   ),
     *     @OA\Response (
     *         response=200,
     *         description="success",
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
        $roles = Role::latest()->paginate(15);
        return new RoleResource($roles);
    }

    /**
     * @OA\Post  (
     *     path="/api/v1/admin/roles",
     *     operationId="store role",
     *     tags={"roles"},
     *     summary="roles",
     *     description="roles",
     *     security={{ "apiAuth": {}},},
     *     @OA\Parameter (
     *      name='"Accept",
     *     in="header",
     *     required=true,
     *     example="application/json",
     *     @OA\Schema (
     *     type="string"
     *         )
     *    ),
     *     @OA\Parameter (
     *     name="Content-Type",
     *     in="header",
     *     required=true,
     *     example="application/json",
     *     @OA\Schema (
     *     type="string"
     *         )
     *   ),
     *     @OA\Response (
     *         response=200,
     *         description="success",
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
    public function store(RoleRequest $request)
    {
        $attrs = $request->validated();
        try {
            DB::beginTransaction();
            $role = Role::create([
                'name' => $attrs['name'],
                'guard_name' => 'web'
            ]);
            $permissions = Permission::all();
            $permissions = $attrs->except('_token', 'name');
            $role->givePermissionTo($permissions);
            DB::commit();
        }catch (\Exception $e)
        {
            DB::rollBack();
            return response(['errors' => $e->getMessage()], 422);
        }
        return new RoleResource($role);
    }

    /**
     * @OA\Put (
     *     path="/api/v1/admin/roles/{role}",
     *     operationId="update role",
     *     tags={"update role"},
     *     summary="update role",
     *     description="update role",
     *     security={{ "apiAuth": {}},},
     *     @OA\Parameter (
     *      name='"Accept",
     *     in="header",
     *     required=true,
     *     example="application/json",
     *     @OA\Schema (
     *     type="string"
     *         )
     *    ),
     *     @OA\Parameter (
     *     name="Content-Type",
     *     in="header",
     *     required=true,
     *     example="application/json",
     *     @OA\Schema (
     *     type="string"
     *         )
     *   ),
     *     @OA\Parameter (
     *     name="id",
     *     in="path",
     *     required=true,
     *     @OA\Schema (
     *     type="integer"
     *         )
     *   ),
     *     @OA\Response (
     *         response=200,
     *         description="success",
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

    public function update(RoleRequest $request, Role $role)
    {
        // validation
        $attrs = $request->validated();
        try {
            DB::beginTransaction();
            $role->update($attrs);
            $permissions = Permission::all();
            $permissions = $attrs->except('_token', 'name', '_method');
            $role->syncPermissions($permissions);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response(['errors' => $e->getMessage()]);
        }
        return new RoleResource($role);
    }
}
