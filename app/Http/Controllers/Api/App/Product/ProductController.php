<?php

namespace App\Http\Controllers\Api\App\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\ProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Http\Resources\Admin\ProductResource;
use App\Http\Services\Image\ImageService;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()->latest()->paginate(15);
        return new ProductResource($products);
    }

    public function store(ProductRequest $request, ImageService $imageService)
    {
        try
        {
            // validation request
            $attrs = $request->validated();
            if ($request->hasFile('picture'))
            {
                $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'products');
                $result = $imageService->save($request->file('picture'));
                // check upload
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

    public function update(UpdateProductRequest $request, Product $product, ImageService $imageService)
    {
        try
        {
            // validation request
            $attrs = $request->validated();
            // check request for upload image
            if ($request->hasFile('picture'))
            {
                // check image exists or not
                if (!empty($product->picture))
                    $imageService->deleteImage($product->picture);
                $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'products');
                $result = $imageService->save($request->file('picture'));
                // check upload
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
