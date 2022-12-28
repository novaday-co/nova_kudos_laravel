<?php

namespace App\Http\Controllers\Api\App\Company\GiftCard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Company\GiftCard\SearchUserRequest;
use App\Http\Requests\Admin\Company\GiftCard\SendGiftRequest;
use App\Http\Resources\Company\GiftCard\GiftCardResource;
use App\Http\Resources\Company\GiftCard\SendGiftCardResource;
use App\Models\Company;
use App\Models\User;

class UserGiftCardController extends Controller
{
    /**
     * @OA\Get (
     *      path="/api/companies/{company_id}/giftCards/client",
     *      operationId="get all gift client",
     *      tags={"companies"},
     *      summary="get all gift client",
     *      description="get all gift client",
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
     *          @OA\JsonContent(ref="/admin/companies/{company_id}/giftCards/client")
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
        $gifts = $company_id->giftCards;
        return GiftCardResource::collection($gifts);
    }

    /**
     * @OA\Post (
     *      path="/api/companies/{company_id}/send/giftCard",
     *      operationId="send gift card",
     *      tags={"companies"},
     *      summary="send gift card",
     *      description="send gift card",
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
     *                  @OA\Property(property="to_id", type="integer", format="integer", example="2"),
     *                   required={"to_id", "gift_id"},
     *                  @OA\Property(property="gift_id", type="integer", format="integer", example=1),
     *                  @OA\Property(property="message", type="string", format="string", example="happy birthday"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/api/companies/{company_id}/send/giftCard")
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
    public function sendGiftCard(SendGiftRequest $request, Company $company_id)
    {
        try {
            $attrs = $request->validated();
            $from_id = $company_id->users()->where('user_id', auth()->user()->id)->first();
            $to_id = $company_id->users()->findOrFail($attrs['to_id']);
            $gift_id = $company_id->giftCards()->findOrFail($attrs['gift_id']);
//            foreach ($from_id as $user)
//            {
                $coin_balance = $from_id->pivot->coin_amount;
                if ($coin_balance < $gift_id->coin)
                    return $this->error([trans('messages.currency.invalid.balance')], trans('messages.currency.invalid.balance'), 422);
                $gift = $company_id->companyUserGiftTransaction()->create([
                    'from_id' => auth()->user()->id,
                    'to_id' => $to_id->id,
                    'gift_id' => $gift_id->id,
                    'message' => $attrs['message']
                ]);
                $gift->load(['company.users' => function($query) use($from_id, $to_id) {
                    $query->whereIn('user_id', [$from_id->id, $to_id->id]);
                }]);
                $coin_balance -= $gift_id->coin;
                $company_id->users()->updateExistingPivot(auth()->user(), array('coin_amount' => $coin_balance));
                return $this->success([SendGiftCardResource::make($gift)], trans('messages.company.giftCard.send'));
//            }
        } catch (\Exception $exception)
        {
            return $this->error([$exception->getMessage()], trans('messages.company.giftCard.invalid.request'), 422);
        }
    }

    /**
     * @OA\Get (
     *      path="/api/companies/{company_id}/search/user",
     *      operationId="search user",
     *      tags={"companies"},
     *      summary="search user",
     *      description="search user",
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
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(property="search", type="string", format="string", example="yasin"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/api/companies/{company_id}/search/user")
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
    public function searchUser(SearchUserRequest $request, Company $company_id)
    {
        $searchItem = $request->search_item;
        $user = $company_id->users()->where('first_name', 'LIKE', "%{$searchItem}%")->get();
        return $user;
    }
}
