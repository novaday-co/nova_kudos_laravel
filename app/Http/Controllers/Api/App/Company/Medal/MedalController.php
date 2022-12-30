<?php

namespace App\Http\Controllers\Api\App\Company\Medal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Medal\MedalRequest;
use App\Http\Requests\Admin\Medal\UpdateMedalRequest;
use App\Http\Resources\Admin\MedalResource;
use App\Models\Company;
use App\Models\Medal;
use Illuminate\Http\Request;

class MedalController extends Controller
{

    /**
     * @OA\Get (
     *      path="/admin/companies/{company_id}/medals",
     *      operationId="get all medal",
     *      tags={"companies"},
     *      summary="get all medal",
     *      description="get all medal",
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
     *      name="company_id",
     *      in="path",
     *      required=true,
     *      example=1,
     *     @OA\Schema(
     *      type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/admin/companies/{company_id}/medals")
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
    public function index(Company $company_id)
    {
        try {
            $medals = $company_id->medals()->latest()->paginate(10);
            return MedalResource::collection($medals);
        } catch (\Exception $exception)
        {
            return $this->error([$exception->getMessage()], trans('messages.company.search.invalid.request'), 422);
        }

    }

    /**
     * @OA\Post (
     *      path="/admin/companies/{company_id}/medals",
     *      operationId="store new medal",
     *      tags={"companies"},
     *      summary="store new medal",
     *      description="store new medal",
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
     *                  @OA\Property(property="name", type="text", format="text", example="gold medal"),
     *                   required={"name", "coin"},
     *                  @OA\Property(property="avatar", type="file", format="text"),
     *                  @OA\Property(property="coin", type="integer", format="integer", example="20"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/admin/companies/{company_id}/giftCards")
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
    public function store(MedalRequest $request, Company $company_id)
    {
        try {
            $attrs = $request->validated();
            if ($request->hasFile('avatar'))
            {
                $avatar = $this->uploadImage($request->file('avatar'), 'images' . DIRECTORY_SEPARATOR
                    . 'companies' . DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . $company_id->id .  DIRECTORY_SEPARATOR . 'medal');
                $attrs['avatar'] = $avatar;
            }
            $medal = $company_id->medals()->create($attrs);
            return new MedalResource($medal);
        } catch (\Exception $exception)
        {
            return $this->error([$exception->getMessage()], trans('messages.company.invalid.request'), 422);
        }
    }

    /**
     * @OA\Post (
     *      path="/admin/companies/{company_id}/medals/{medal_id}",
     *      operationId="update medal",
     *      tags={"companies"},
     *      summary="update medal",
     *      description="update medal",
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
     *    @OA\Parameter(
     *      name="medal_id",
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
     *                  @OA\Property(property="name", type="text", format="text", example="gold medal"),
     *                  @OA\Property(property="avatar", type="file", format="text"),
     *                  @OA\Property(property="coin", type="integer", format="integer", example="20"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/admin/companies/{company_id}/medals/{medal_id}")
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
    public function update(UpdateMedalRequest $request, Company $company_id, Medal $medal_id)
    {
        try {
            $attrs = $request->validated();
            $medal = $company_id->medals()->where('id', $medal_id->id)->firstOrFail();
            if ($request->hasFile('avatar')) {
                $avatar = $this->uploadImage($request->file('avatar'), 'images' . DIRECTORY_SEPARATOR . 'companies' .
                    DIRECTORY_SEPARATOR . 'company' . DIRECTORY_SEPARATOR . $company_id->id . DIRECTORY_SEPARATOR . 'medal');
                $attrs['icon'] = $avatar;
            }
            $medal->update($attrs);
            return MedalResource::make($medal);
        } catch (\Exception $e)
        {
            return $this->error([$e->getMessage()], trans('messages.company.search.invalid.medal'), 422);
        }
    }

    /**
     * @OA\Get (
     *      path="/admin/companies/{company_id}/search/medal",
     *      operationId="search medal",
     *      tags={"companies"},
     *      summary="search medal",
     *      description="search medal",
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
     *      name="company_id",
     *      in="path",
     *      required=true,
     *      example=1,
     *     @OA\Schema(
     *      type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *      name="search",
     *      in="query",
     *      required=false,
     *     description="search medal",
     *      example="gold medal",
     *     @OA\Schema(
     *      type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/admin/companies/{company_id}/search/medal")
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
    public function searchMedal(Request $request, Company $company_id)
    {
        try {
            if ($request->has('search'))
            {
                $medal = $company_id->medals()->where('name', 'LIKE', "%{$request->search}%")->firstOrFail();
                return MedalResource::make($medal);
            }
            if ($request->has('search') == '')
            {
                $medal = $company_id->medals()->limit(5)->orderBy('created_at', 'desc')->get();
                return MedalResource::collection($medal);
            }
        } catch (\Exception $exception)
        {
            return $this->error([$exception->getMessage()], trans('messages.company.search.invalid.medal'), 422);
        }
    }
}
