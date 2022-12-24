<?php

namespace App\Http\Controllers\Api\App\Company\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\ProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Http\Resources\Admin\ProductResource;
use App\Models\Company;
use App\Models\Product;
use App\Services\Image\ImageService;

class ProductController extends Controller
{

    /**
     * @OA\Get (
     *      path="/admin/companies/{company_id}/markets/products",
     *      operationId="get products of market",
     *      tags={"companies"},
     *      summary="get products of market",
     *      description="get products of market",
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
     *          @OA\JsonContent(ref="/api/companies/markets/all")
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
        $products = $company_id->products()->latest()->paginate(10);
        // $products = Product::query()->latest()->paginate(15);
        return new ProductResource($products);
    }

    /**
     * @OA\Post (
     *      path="/api/companies/{company_id}/market/products",
     *      operationId="store new product",
     *      tags={"companies"},
     *      summary="store new product",
     *      description="store new product",
     *      security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *          name="company_id",
     *          in="path",
     *          required=true,
     *          example=1,
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
     *                  @OA\Property(property="name", type="text", format="text", example="text"),
     *                   required={"name"},
     *                  @OA\Property(property="currency", type="text", format="text", example="text"),
     *                   required={"coin"},
     *                  @OA\Property(property="amount", type="integer", format="integer", example="1213"),
     *                   required={"amount"},
     *                  @OA\Property(property="avatar", type="file", format="file", example="text"),
     *                  @OA\Property(property="expiration_date", type="text", format="text", example="text"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/api/companies/{company_id}/products")
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
    public function store(ProductRequest $request, Company $company_id)
    {
        try
        {
            $attrs = $request->validated();
            if ($request->hasFile('avatar'))
            {
                $avatar = $this->uploadImage($request->file('avatar'),'companies' . DIRECTORY_SEPARATOR . $company_id->id . DIRECTORY_SEPARATOR . 'market');
                $attrs['avatar'] = $avatar;
            }
            $product = $company_id->products()->create($attrs);
            return new ProductResource($product);
        } catch (\Exception $e)
        {
            return $this->error([$e->getMessage()], trans('messages.market.invalid.request'), 422);
        }
    }

    /**
     * @OA\Put(
     *      path="/api/companies/{company_id}/market/products/{product}",
     *      operationId="update product",
     *      tags={"products"},
     *      summary="update product",
     *      description="update product",
     *      security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *          name="product",
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
     *                  @OA\Property(property="name", type="text", format="text", example="text"),
     *                   required={"name"},
     *                  @OA\Property(property="currency", type="text", format="text", example="text"),
     *                   required={"coin"},
     *                  @OA\Property(property="amount", type="integer", format="integer", example="1213"),
     *                   required={"amount"},
     *                  @OA\Property(property="avatar", type="file", format="file", example="text"),
     *                  @OA\Property(property="expiration_date", type="text", format="text", example="text"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *     @OA\JsonContent(ref="/api/companies/{company_id}/market/products/{product}")
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
    public function update(UpdateProductRequest $request, Product $product, Company $company_id)
    {
        try
        {
            $attrs = $request->validated();
            if ($request->hasFile('avatar'))
            {
             //   if (!empty($product->picture))
               //     $imageService->deleteImage($product->picture);
                $avatar = $this->uploadImage($request->file('avatar'), 'companies' . DIRECTORY_SEPARATOR . $company_id->id . DIRECTORY_SEPARATOR . 'market');
                $attrs['avatar'] = $avatar;
            }
            $product = $company_id->products()->findOrFail($product)->update($attrs);
            return new ProductResource($product);
        }
        catch (\Exception $e)
        {
            return $this->error([$e->getMessage()], trans('messages.market.invalid.request'), 400);
        }
    }

    public function destroy(Product $product, Company $company_id)
    {
        try
        {
            $product = $company_id->products()->delete();
            return $this->success([trans('messages.market.destroy')], trans('messages.market.destroy'));
        } catch (\Exception $e)
        {
            return $this->error([$e->getMessage()], trans('messages.market.invalid.request'), 422);
        }
    }
}
