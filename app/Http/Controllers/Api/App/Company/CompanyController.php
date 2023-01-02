<?php

namespace App\Http\Controllers\Api\App\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Company\Setting\CompanySettingRequest;
use App\Http\Requests\Admin\Company\UpdateCompanyRequest;
use App\Http\Requests\SuperAdmin\Company\CompanyRequest;
use App\Http\Resources\Admin\GroupResource;
use App\Http\Resources\Company\Setting\CompanySettingResource;
use App\Http\Resources\SuperAdmin\CompanyResource;
use App\Http\Resources\SuperAdmin\OwnerCompanyResource;
use App\Http\Resources\UserResource;
use App\Models\Company;
use App\Models\OwnerCompany;
use App\Models\User;
use App\Services\Image\ImageService;

class CompanyController extends Controller
{

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
     *      path="/companies/store",
     *      operationId="store new company",
     *      tags={"Company"},
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
     *      path="/companies/{company_id}/update",
     *      operationId="update company",
     *      tags={"Company"},
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
     *          @OA\JsonContent(ref="/companies/{company_id}/update")
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
     *      path="/companies/{company_id}/setting",
     *      operationId="update company setting",
     *      tags={"Company"},
     *      summary="update company setting",
     *      description="update company setting",
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
     *                  @OA\Property(property="withdrawal_permission", type="text", format="text", example="enable"),
     *                   required={"withdrawal_permission", "min_withdrawal"},
     *                  @OA\Property(property="min_withdrawal", type="text", format="text", example=200000),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/companies/{company_id}/setting")
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

    public function setSetting(CompanySettingRequest $request, Company $company_id)
    {
        try {
          $attrs = $request->validated();
          $company_id->updateOrFail([
              'withdrawal_permission' => $attrs['withdrawal_permission'],
              'min_withdrawal' => $attrs['min_withdrawal'],
          ]);
          return CompanySettingResource::make($company_id);
        } catch (\Exception $exception)
        {
            return $this->error([trans('messages.company.setting.invalid.request')], trans('messages.company.setting.invalid.request'), 500);
        }
    }

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
