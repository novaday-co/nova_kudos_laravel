<?php

namespace App\Http\Controllers\Api\App\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Company\UpdateCompanyRequest;
use App\Http\Requests\SuperAdmin\Company\CompanyRequest;
use App\Http\Resources\Admin\GroupResource;
use App\Http\Resources\SuperAdmin\CompanyResource;
use App\Http\Resources\SuperAdmin\OwnerCompanyResource;
use App\Http\Resources\UserResource;
use App\Models\Company;
use App\Models\OwnerCompany;
use App\Models\User;
use App\Services\Image\ImageService;

class CompanyController extends Controller
{
    /**
     * @OA\Get (
     *      path="/admin/companies/lists",
     *      operationId="get company lists",
     *      tags={"companies"},
     *      summary="get company lists",
     *      description="get company list",
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
     *          @OA\JsonContent(ref="/admin/companies/lists")
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
    public function companyLists()
    {
        try {
            $companies = Company::query()->latest()->paginate(10);
            return CompanyResource::collection($companies);
        } catch (\Exception $exception)
        {
            return response(['bad request' => $exception->getMessage()]);
        }
    }

    /**
     * @OA\Get (
     *      path="/admin/companies/{company}/groups",
     *      operationId="get company groups",
     *      tags={"companies"},
     *      summary="get company groups",
     *      description="get company groups",
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
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/admin/companies/lists")
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
    public function companyGroups(Company $company)
    {
        try {
            $groups = $company->groups;
            return GroupResource::collection($groups);
        } catch (\Exception $exception)
        {
            return response(['bad request' => $exception->getMessage()], 400);
        }
    }

    /**
     * @OA\Get (
     *      path="/admin/companies/{company}/users",
     *      operationId="get all users of company",
     *      tags={"companies"},
     *      summary="get all users of company",
     *      description="get all users of company",
     *      security={ {"sanctum": {} }},
     *      @OA\Parameter(
     *          name="company",
     *          in="path",
     *          required=true,
     *         @OA\Schema(
     *           type="integer"
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
     *          @OA\JsonContent(ref="/superAdmin/companies")
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
    public function companyUsers(Company $company)
    {
        try {
            $users = $company->users;
            return UserResource::collection($users);
        } catch (\Exception $exception)
        {
            return response(['bad request' => $exception->getMessage()]);
        }
    }

    /**
     * @OA\Get  (
     *      path="/admin/companies/{company}/owner/get",
     *      operationId="get owner",
     *      tags={"companies"},
     *      summary="get owner",
     *      description="get owner",
     *      security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *         description="company parameter",
     *         in="path",
     *         name="company",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
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
     *          @OA\JsonContent(ref="/admin/companies/{company}/owner/get")
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
    public function companyOwner(Company $company)
    {
        try
        {
            $owner = OwnerCompany::query()->findOrFail([
                'company_id' => $company->id
            ]);
            return OwnerCompanyResource::collection($owner);
        } catch (\Exception $exception)
        {
            return response(['bad request' => $exception->getMessage()], 400);
        }
    }

    /**
     * @OA\Post (
     *      path="/admin/companies/store",
     *      operationId="store new company",
     *      tags={"companies"},
     *      summary="store new company",
     *      description="store new company",
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
     *          @OA\JsonContent(ref="/admin/companies/store")
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
    public function store(CompanyRequest $request)
    {
        try {
            $attrs = $request->validated();
            if ($request->hasFile('avatar')) {
                $avatar = $this->uploadImage($request->file('avatar'), 'images' . DIRECTORY_SEPARATOR
                . 'companies' . DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . 'avatar');
                $attrs['avatar'] = $avatar;
            }
            $company = Company::query()->create($attrs);
            return new CompanyResource($company);
        } catch (\Exception $e) {
            return $this->error([$e->getMessage()], trans('messages.company.invalid.request'), 422);
        }
    }

    /**
     * @OA\Post (
     *      path="/admin/companies/{company_id}/update",
     *      operationId="update company",
     *      tags={"companies"},
     *      summary="update company",
     *      description="update company",
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
     *    @OA\Parameter(
     *      name="company_id",
     *      in="path",
     *      required=true,
     *      example=1,
     *     @OA\Schema(
     *      type="integer"
     *          )
     *      ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="name", type="text", format="text", example="novaday"),
     *                   required={"name"},
     *                  @OA\Property(property="avatar", type="file", format="text"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/superAdmin/companies/{company}/update")
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

    public function update(UpdateCompanyRequest $request, Company $company_id)
    {
        try {
            $attrs = $request->validated();
            if ($request->hasFile('avatar')) {
              $avatar = $this->uploadImage($request->file('avatar'), 'images' . DIRECTORY_SEPARATOR
              . 'companies' . DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . 'avatar');
                $attrs['avatar'] = $avatar;
            }
            $company_id->update($attrs);
            return CompanyResource::make($company_id);
        } catch (\Exception $e) {
            return $this->error([$e->getMessage()], trans('messages.company.invalid.request'), 422);
        }
    }

    /**
     * @OA\Post (
     *      path="/admin/companies/{company}/owner/users/{user}",
     *      operationId="add owner to company",
     *      tags={"companies"},
     *      summary="add owner to company",
     *      description="add owner to company",
     *      security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *         description="company parameter",
     *         in="path",
     *         name="company",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Parameter(
     *          description="user parameter",
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
     *          @OA\JsonContent(ref="/superAdmin/companies/{company}/users/{user}")
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
      public function addOwner(Company $company, User $user)
      {
         try {
              $owner = OwnerCompany::query()->firstOrCreate([
                  'company_id' => $company->id,
                  'user_id' => $user->id,
              ], [
                 'company_id' => $company->id,
                  'user_id' => $user->id,
              ]);
              return new OwnerCompanyResource($owner);
    } catch (\Exception $exception) {
             return response('error', 400);
         }
      }

    /**
     * @OA\Post (
     *      path="/admin/companies/{company}/users/{user}",
     *      operationId="add user to company",
     *      tags={"companies"},
     *      summary="add user to company",
     *      description="add user to company",
     *      security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *         description="company parameter",
     *         in="path",
     *         name="company",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Parameter(
     *          description="user parameter",
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
     *          @OA\JsonContent(ref="/superAdmin/companies/{company}/users/{user}")
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
    public function addUser(Company $company, User $user)
    {
        try {
            $company->users()->attach($user);
            return response('add user');
        } catch (\Exception $exception)
        {
            return response(['bad request' => $exception->getMessage()], 400);
        }
    }

    /**
     * @OA\Delete  (
     *      path="/admin/companies/{company}/users/{user}",
     *      operationId="remove user",
     *      tags={"companies"},
     *      summary="remove user",
     *      description="remove user",
     *      security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *         description="company parameter",
     *         in="path",
     *         name="company",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\Parameter(
     *          description="user parameter",
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
     *          @OA\JsonContent(ref="/admin/companies/{company}/users/{user}")
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
    public function removeUser(Company $company, User $user)
    {
        try
        {
            $company->users()->detach($user);
            return response(['this user deleted']);
        } catch (\Exception $exception)
        {
            return response(['bad request' => $exception->getMessage()], 400);
        }
    }
}
