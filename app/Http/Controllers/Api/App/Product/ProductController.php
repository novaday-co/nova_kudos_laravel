<?php

namespace App\Http\Controllers\Api\App\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\ProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Http\Resources\Admin\ProductResource;
use App\Models\Product;
use App\Services\Image\ImageService;

class ProductController extends Controller
{

    /**
     * @OA\Get (
     *      path="/api/app/products/all",
     *      operationId="get products",
     *      tags={"products"},
     *      summary="get products",
     *      description="get products",
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
     *          @OA\JsonContent(ref="/api/app/products/all")
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
        $products = Product::query()->latest()->paginate(15);
        return new ProductResource($products);
    }

    /**
     * @OA\Post (
     *      path="/api/app/products/companies/{company}",
     *      operationId="store new product",
     *      tags={"products"},
     *      summary="store new product",
     *      description="store new product",
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
     *                  @OA\Property(property="name", type="text", format="text", example="text"),
     *                   required={"name"},
     *                  @OA\Property(property="coin", type="text", format="text", example="text"),
     *                   required={"coin"},
     *                  @OA\Property(property="amount", type="integer", format="integer", example="1213"),
     *                   required={"amount"},
     *                  @OA\Property(property="picture", type="file", format="file", example="text"),
     *                  @OA\Property(property="expiration_date", type="text", format="text", example="text"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/api/app/products/companies/{company}")
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
    public function store(ProductRequest $request, ImageService $imageService)
    {
        try
        {
            $attrs = $request->validated();
            if ($request->hasFile('picture'))
            {
                $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'products');
                $result = $imageService->save($request->file('picture'));
                if ($result === false)
                    return response('error uploading photo ', 400);
                $attrs['picture'] = $result;
            }
            $product = Product::query()->create($attrs);
            return new ProductResource($product);
        } catch (\Exception $e)
        {
            return response(['bad request: ' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Put(
     *      path="/api/app/products/{product}",
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
     *                  @OA\Property(property="coin", type="text", format="text", example="text"),
     *                   required={"coin"},
     *                  @OA\Property(property="amount", type="integer", format="integer", example="1213"),
     *                   required={"amount"},
     *                  @OA\Property(property="picture", type="file", format="file", example="text"),
     *                  @OA\Property(property="expiration_date", type="text", format="text", example="text"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *     @OA\JsonContent(ref="/api/app/products/{product}")
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
    public function update(UpdateProductRequest $request, Product $product, ImageService $imageService)
    {
        try
        {
            $attrs = $request->validated();
            if ($request->hasFile('picture'))
            {
                if (!empty($product->picture))
                    $imageService->deleteImage($product->picture);
                $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'products');
                $result = $imageService->save($request->file('picture'));
                if ($result === false)
                    return response('error uploading photo ', 400);
                $attrs['picture'] = $result;
            }
            $product = $product->update($attrs);
            return new ProductResource($product);
        }
        catch (\Exception $e)
        {
            return response(['bad request' => $e->getMessage()], 400);
        }
    }

    public function destroy(Product $product)
    {
        try
        {
            $product->delete();
            return response('success', 200);
        } catch (\Exception $e)
        {
            return response(['bad request' => $e->getMessage()], 400);
        }
    }
}
