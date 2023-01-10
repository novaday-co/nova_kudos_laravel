<?php

namespace App\Http\Controllers\Api\App\Company\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Company\Product\ProductRequest;
use App\Http\Requests\Admin\Company\Product\UpdateProductRequest;
use App\Http\Resources\Admin\ProductResource;
use App\Http\Resources\Company\Balance\TransactionUserResource;
use App\Models\Company;
use App\Models\Product;
use Illuminate\Http\Request;


class ProductController extends Controller
{

    /**
     * @OA\Get (
     *      path="/companies/{company_id}/market/products",
     *      operationId="get products of market",
     *      tags={"Company"},
     *      summary="get products of market",
     *      description="get products of market",
     *      security={ {"sanctum": {} }},
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
     *          name="query_count",
     *          in="query",
     *          required=false,
     *          example=5,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/companies/{company_id}/markets/products")
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
    public function index(Request $request, Company $company_id)
    {
        if ($request->has('query_count'))
        {
            $products = $company_id->products()->latest()->paginate((int) $request->get('query_count', 10));
            return ProductResource::collection($products);
        }
        $products = $company_id->products()->latest()->paginate(10);
        return ProductResource::collection($products);
    }

    /**
     * @OA\Post (
     *      path="/companies/{company_id}/market/products",
     *      operationId="store new product",
     *      tags={"Company"},
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
     *                  @OA\Property(property="name", type="text", format="text", example="prdouct 1"),
     *                   required={"name", "currency", "amount"},
     *                  @OA\Property(property="currency", type="integer", format="integer", example="1220"),
     *                  @OA\Property(property="amount", type="integer", format="integer", example="12"),
     *                  @OA\Property(property="avatar", type="file", format="file"),
     *                  @OA\Property(property="expiration_date", type="text", format="text", example="2022-12-11"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/companies/{company_id}/products")
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
            $avatar = $this->uploadImage($request, 'images' . DIRECTORY_SEPARATOR . 'companies' . DIRECTORY_SEPARATOR . 'company'
                . DIRECTORY_SEPARATOR . $company_id->id . DIRECTORY_SEPARATOR . 'market');
            $attrs['avatar'] = $avatar;
            $coin_value = $company_id->coin->coin_value;
            $coin = $attrs['currency'] / $coin_value;
            $attrs['coin'] = round($coin);
            $product = $company_id->products()->create($attrs);
            return new ProductResource($product);
        } catch (\Exception $e)
        {
            return $this->error([$e->getMessage()], trans('messages.company.market.invalid.request'), 422);
        }
    }

    /**
     * @OA\Post (
     *      path="/companies/{company_id}/market/products/{product}",
     *      operationId="update product",
     *      tags={"Company"},
     *      summary="update product",
     *      description="update product",
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
     *     @OA\Parameter(
     *          name="product",
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
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="name", type="text", format="text", example="product 1"),
     *                  @OA\Property(property="currency", type="text", format="text", example="3000"),
     *                  @OA\Property(property="amount", type="integer", format="integer", example="12"),
     *                  @OA\Property(property="avatar", type="file", format="file"),
     *                  @OA\Property(property="expiration_date", type="text", format="text", example="2022-12-11"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *     @OA\JsonContent(ref="/companies/{company_id}/market/products/{product}")
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
    public function update(UpdateProductRequest $request, Company $company_id, Product $product)
    {
//        try
//        {
            $attrs = $request->validated();
            $avatar = $this->uploadImage($request, 'images' . DIRECTORY_SEPARATOR . 'companies' . DIRECTORY_SEPARATOR . 'company'
            . DIRECTORY_SEPARATOR . $company_id->id . DIRECTORY_SEPARATOR . 'market');
            $attrs['avatar'] = $avatar;
            $coin_value = $company_id->coin->coin_value;
            if ($attrs['currency'])
            {
                $coin = $attrs['currency'] / $coin_value;
                $attrs['coin'] = round($coin);
            }
            $product->company()->where('id', $company_id->id)->get();
            $product->update($attrs);
             return ProductResource::make($product);
        // }
//        catch (\Exception $e)
//        {
//            return $this->error([$e->getMessage()], trans('messages.company.market.invalid.request'), 400);
//        }
    }

    /**
     * @OA\Delete (
     *      path="/companies/{company_id}/market/products/{product}",
     *      operationId="delete product",
     *      tags={"Company"},
     *      summary="delete product",
     *      description="delete product",
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
     *     @OA\Parameter(
     *          name="product",
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
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *     @OA\JsonContent(ref="/companies/{company_id}/market/products/{product}")
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
    public function destroy(Company $company_id, Product $product)
    {
        try
        {
            $company_id->products()->where('id', $product->id)->delete();
            return $this->success([trans('messages.company.market.destroy')], trans('messages.company.market.destroy'));
        } catch (\Exception $e)
        {
            return $this->error([trans('messages.company.market.invalid.request')], trans('messages.company.market.invalid.request'), 422);
        }
    }

    public function buyProduct()
    {

    }
}
