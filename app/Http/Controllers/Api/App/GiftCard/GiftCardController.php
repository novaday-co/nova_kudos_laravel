<?php

namespace App\Http\Controllers\Api\App\GiftCard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GiftCard\GiftCardRequest;
use App\Http\Requests\Admin\GiftCard\UpdateGiftCardRequest;
use App\Http\Resources\Admin\GiftCardResource;
use App\Http\Services\Image\ImageService;
use App\Models\Company;
use App\Models\GiftCard;

class GiftCardController extends Controller
{
    /**
     * @OA\Get (
     *      path="/api/giftcards/all",
     *      operationId="get gift cards",
     *      tags={"giftcards"},
     *      summary="get giftcards",
     *      description="get giftcards",
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
     *          @OA\JsonContent(ref="/api/giftcards/all")
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
        $giftCards = GiftCard::query()->latest()->paginate(15);
        return new GiftCardResource($giftCards);
    }

    /**
     * @OA\Post (
     *      path="/api/giftcards/companies/{company}/store",
     *      operationId="store new gift card",
     *      tags={"giftcards"},
     *      summary="store new git card",
     *      description="store new gift card",
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
     *                  @OA\Property(property="title", type="text", format="text", example="yasin"),
     *                   required={"title"},
     *                  @OA\Property(property="picture", type="file", format="text"),
     *                  @OA\Property(property="coin", type="integer", format="integer", example="123"),
     *                  @OA\Property(property="expiration_date", type="string", format="string", example="12/3/22"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/api/giftcards/companies/{company}/store")
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

    public function store(GiftCardRequest $request, Company $company, ImageService $imageService)
    {
        try
        {
            $attrs = $request->validated();
            if ($request->hasFile('picture'))
            {
                $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'giftcards');
                $result = $imageService->save($request->file('picture'));
                if ($result === false)
                    return response('error uploading photo ', 400);
                $attrs['picture'] = $result;
            }
            $attrs['company_id'] = $company->id;
            $giftCard = GiftCard::query()->create($attrs);
            return new GiftCardResource($giftCard);
        } catch (\Exception $e)
        {
            return response(['bad request' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Put(
     *      path="/api/app/giftcards/{giftcard}/update",
     *      operationId="update gift card",
     *      tags={"giftcards"},
     *      summary="update giftcard",
     *      description="update giftcard",
     *      security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *          name="giftcard",
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

     *                  @OA\Property(property="title", type="text", format="text", example="yasin"),
     *                   required={"title"},
     *                  @OA\Property(property="picture", type="file", format="text"),
     *                  @OA\Property(property="coin", type="integer", format="integer", example="123"),
     *                  @OA\Property(property="expiration_date", type="string", format="string", example="12/3/22"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/api/app/giftcards/{giftcard}/update")
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

    public function update(UpdateGiftCardRequest $request, GiftCard $giftCard, ImageService $imageService)
    {
        try
        {
            $attrs = $request->validated();
            if ($request->hasFile('picture'))
            {
                if (!empty($giftCard->picture))
                    $imageService->deleteImage($giftCard->picture);
                $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'giftcards');
                $result = $imageService->save($request->file('picture'));
                if ($result === false)
                    return response('error uploading photo ', 400);
                $attrs['picture'] = $result;
            }
            $giftCard = $giftCard->update($attrs);
            return new GiftCardResource($giftCard);
        }
        catch (\Exception $e)
        {
            return response(['bad request' => $e->getMessage()], 400);
        }
    }

    /*public function destroy(GiftCard $giftCard)
    {
        try
        {
            $giftCard->delete();
            return response('delete success', 200);
        }
        catch (\Exception $e)
        {
            return response(['bad request' => $e->getMessage()], 400);
        }
    }*/
}
