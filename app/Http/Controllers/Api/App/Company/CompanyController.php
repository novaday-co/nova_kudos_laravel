<?php

namespace App\Http\Controllers\Api\App\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Group\GroupRequest;
use App\Http\Requests\Admin\Group\UpdateGroupRequest;
use App\Http\Requests\SuperAdmin\Company\CompanyRequest;
use App\Http\Resources\Admin\GroupResource;
use App\Http\Resources\Auth\UserResource;
use App\Http\Resources\SuperAdmin\CompanyResource;
use App\Http\Resources\SuperAdmin\OwnerCompanyResource;
use App\Http\Services\Image\ImageService;
use App\Models\Company;
use App\Models\Group;
use App\Models\OwnerCompany;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    /**
     * @OA\Get (
     *      path="/superAdmin/companies/all",
     *      operationId="get all companies",
     *      tags={"companies"},
     *      summary="get all companies",
     *      description="get all companies",
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
    public function index()
    {
        $companies = Company::query()->latest()->paginate(15);
        return CompanyResource::collection($companies);
    }

    /**
     * @OA\Post (
     *      path="/superAdmin/companies/store",
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

    public function store(CompanyRequest $request, ImageService $imageService)
    {
        try
        {
            $attrs = $request->validated();
            if ($request->hasFile('avatar')) {
                $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'companies');
                $result = $imageService->save($request->file('avatar'));
                if ($result === false)
                    return response('error uploading photo ', 400);
                $attrs['avatar'] = $result;
            }
            $company = Company::query()->create($attrs);
            return new CompanyResource($company);
        } catch (\Exception $e)
        {
            return response(['errors: ' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Put(
     *      path="/superAdmin/companies/{company}/update",
     *      operationId="update company",
     *      tags={"companies"},
     *      summary="update company",
     *      description="update company",
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
     *          @OA\JsonContent(ref="/superAdmin/companies/{company}")
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

    public function update(CompanyRequest $request, ImageService $imageService, Company $company)
    {
        try
        {
            $attrs = $request->validated();
            if ($request->hasFile('avatar')) {
                if (!empty($company->avatar))
                    $imageService->deleteImage($company->avatar);
                $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'companies');
                $result = $imageService->save($request->file('avatar'));
                if ($result === false)
                    return response('error uploading photo ', 400);
                $attrs['avatar'] = $result;
            }
            $company->update($attrs);
            return new CompanyResource($company);
        } catch (\Exception $e)
        {
            return response(['errors' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Post (
     *      path="/superAdmin/companies/{company}/users/{user}",
     *      operationId="add owner to company",
     *      tags={"companies"},
     *      summary="add owner to company",
     *      description="add owner to company",
     *      security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *          name="company",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Parameter(
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

    public function addOwner(User $user, Company $company)
    {
        try {
            $owner = OwnerCompany::query()->firstOrCreate([
                'user_id' => $user->id,
                'company_id' => $company->id,
            ], [
               'user_id' => $user->id,
               'company_id' => $company->id,
            ]);
            return new OwnerCompanyResource($owner);
        } catch (\Exception $exception) {
            return response('error', 400);
        }
    }
}
