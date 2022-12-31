<?php

namespace App\Http\Controllers\Api\App\Company\GiftCard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Company\GiftCard\SearchUserRequest;
use App\Http\Requests\Admin\Company\GiftCard\SendGiftRequest;
use App\Http\Resources\Company\GiftCard\GiftCardResource;
use App\Http\Resources\Company\GiftCard\SendGiftCardResource;
use App\Http\Resources\Company\User\Search\UserSearchResource;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class UserGiftCardController extends Controller
{
    /**
     * @OA\Get (
     *      path="/users/companies/{company_id}/giftCards",
     *      operationId="get all gift User",
     *      tags={"User"},
     *      summary="get all gift User",
     *      description="get all gift User",
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
     *          @OA\JsonContent(ref="/users/companies/{company_id}/giftCards")
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
     *      path="/users/companies/{company_id}/send/giftCard",
     *      operationId="send gift card",
     *      tags={"User"},
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
     *          @OA\JsonContent(ref="/users/companies/{company_id}/send/giftCard")
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
     *      path="/users/companies/{company_id}/search/user",
     *      operationId="search user",
     *      tags={"User"},
     *      summary="search user",
     *      description="search user",
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
     *     description="search user with firstname or lastname",
     *      example="yasin",
     *     @OA\Schema(
     *      type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/users/companies/{company_id}/search/user")
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
    public function searchUser(Request $request, Company $company_id)
    {
        try {
            if ($request->has('search'))
            {
                $user = $company_id->users()->where('first_name', 'LIKE', "%{$request->search}%")
                    ->orWhere('last_name', 'LIKE', "%{$request->search}%")->firstOrFail();
                $user->load(['companies' => function($query) use ($user) {
                    $query->whereIn('user_id', [$user->id]);
                }]);
                return UserSearchResource::make($user);
            } if ($request->has('search') == '') {
                $user = $company_id->users()->limit(5)->orderBy('created_at', 'desc')->get();
                return UserSearchResource::collection($user);
            }
        } catch (\Exception $exception)
        {
            return $this->error([$exception->getMessage()], trans('messages.company.search.invalid.user'), 422);
        }
    }
}
