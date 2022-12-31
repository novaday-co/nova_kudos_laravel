<?php

namespace App\Http\Controllers\Api\App\Company\GiftCard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Company\GiftCard\GiftCardRequest;
use App\Http\Requests\Admin\Company\GiftCard\UpdateGiftCardRequest;
use App\Http\Resources\Company\GiftCard\GiftCardResource;
use App\Models\Company;
use App\Models\GiftCard;

class GiftCardController extends Controller
{
    /**
     * @OA\Get (
     *      path="/companies/{company_id}/giftCards",
     *      operationId="get all gift cards",
     *      tags={"Company"},
     *      summary="get all giftcards",
     *      description="get all giftcards",
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
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/companies/{company_id}/giftCards")
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
        $giftCards = $company_id->giftCards;
        return GiftCardResource::collection($giftCards);
    }

    /**
     * @OA\Post (
     *      path="/companies/{company_id}/giftCards",
     *      operationId="store new gift card",
     *      tags={"Company"},
     *      summary="store new gift card",
     *      description="store new gift card",
     *      security={ {"sanctum": {} }},
     *    @OA\Parameter(
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
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="title", type="text", format="text", example="gift 1"),
     *                   required={"title", "coin"},
     *                  @OA\Property(property="avatar", type="file", format="text"),
     *                  @OA\Property(property="coin", type="integer", format="integer", example="20"),
     *                  @OA\Property(property="expiration_date", type="string", format="string", example="2022-12-27"),
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

    public function store(GiftCardRequest $request, Company $company_id)
    {
        try
        {
            $attrs = $request->validated();
            if ($request->hasFile('avatar'))
            {
                $avatar = $this->uploadImage($request->file('avatar'), 'images' . DIRECTORY_SEPARATOR . 'companies' . DIRECTORY_SEPARATOR . $company_id->id . DIRECTORY_SEPARATOR . 'giftCards');
                $attrs['avatar'] = $avatar;
            }
            $giftCard = $company_id->giftCards()->create([
               'title' => $attrs['title'],
               'avatar' => $avatar,
               'coin' => $attrs['coin'],
               'expiration_date' => $attrs['expiration_date']
            ]);
            return new GiftCardResource($giftCard);
        } catch (\Exception $e)
        {
            return $this->error([$e->getMessage()], trans('messages.company.giftcard.failed'), 422);
        }
    }

    /**
     * @OA\Post (
     *      path="/companies/{company_id}/giftCards/{giftCard}",
     *      operationId="update gift card",
     *      tags={"Company"},
     *      summary="update gift card",
     *      description="update gift card",
     *      security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *      name="company_id",
     *      in="path",
     *      required=true,
     *      example=1,
     *     @OA\Schema(
     *      type="integer"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="giftCard",
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

     *                  @OA\Property(property="title", type="text", format="text", example="birthday gift"),
     *                  @OA\Property(property="avatar", type="file", format="text"),
     *                  @OA\Property(property="coin", type="integer", format="integer", example="20"),
     *                  @OA\Property(property="expiration_date", type="string", format="string", example="2023-12-29"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/admin/companies/{company_id}/giftCards/{giftCard}")
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

    public function update(UpdateGiftCardRequest $request, Company $company_id, GiftCard $giftCard)
    {
        try
        {
            $attrs = $request->validated();
            if ($request->hasFile('avatar'))
            {
                $avatar = $this->uploadImage($request->file('avatar'), 'images' . DIRECTORY_SEPARATOR . 'companies'
                    . DIRECTORY_SEPARATOR . $company_id->id . DIRECTORY_SEPARATOR . 'giftCard');
                $attrs['avatar'] = $avatar;
            }
            $gift = $company_id->giftCards()->findOrFail($giftCard->id);
            $gift->update([
                'title' => $attrs['title'],
                'coin' => $attrs['coin'],
                'avatar' => $avatar,
                'expiration_date' => $attrs['expiration_date'],
            ]);
            return GiftCardResource::make($gift);
        }
        catch (\Exception $e)
        {
            return $this->error([$e->getMessage()],trans('messages.company.giftcard.failed'), 422);
        }
    }

    /**
     * @OA\Delete  (
     *      path="/companies/{company_id}/giftCards/{giftCard}",
     *      operationId="delete gift card",
     *      tags={"Company"},
     *      summary="delete gift card",
     *      description="delete gift card",
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
     *     @OA\Parameter(
     *          name="giftCard",
     *          in="path",
     *          required=true,
     *          example=1,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/admin/companies/{company_id}/giftCards/{giftCard}")
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

    public function destroy(Company $company_id, GiftCard $giftCard)
    {
        try {
            $company_id->giftCards()->findOrFail($giftCard->id);
            $giftCard->delete();
            return $this->success([], trans('messages.company.giftCard.success.delete'));
        } catch (\Exception $exception)
        {
            return $this->error([$exception->getMessage()], trans('messages.company.giftCard.invalid.delete'), 422);
        }
    }
}



