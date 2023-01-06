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
     * @OA\Post (
     *      path="/users/send-gift-card",
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
     *          @OA\JsonContent(ref="/users/send-gift-card")
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
    public function sendGiftCard(SendGiftRequest $request)
    {
        try {
            $attrs = $request->validated();
            $userId = auth()->user();
            $company_user = $userId->companies()->where('company_id', $userId->default_company)->firstOrFail();
            $to_id = $company_user->users()->findOrFail($attrs['to_id']);
            $gift_id = $company_user->giftCards()->findOrFail($attrs['gift_id']);
            $coin_balance = $company_user->pivot->coin_amount;
            if ($coin_balance < $gift_id->coin)
                return $this->error([trans('messages.currency.invalid.balance')], trans('messages.currency.invalid.balance'), 422);
            $gift = $company_user->companyUserGiftTransaction()->create([
                'from_id' => auth()->user()->id,
                'to_id' => $to_id->id,
                'gift_id' => $gift_id->id,
                'message' => $attrs['message']
            ]);
            $gift->load(['company.users' => function($query) use($company_user, $to_id) {
                $query->whereIn('user_id', [$company_user->id, $to_id->id]);
            }]);
            $coin_balance -= $gift_id->coin;
            $company_user->users()->updateExistingPivot(auth()->user(), array('coin_amount' => $coin_balance));
            return $this->success([SendGiftCardResource::make($gift)], trans('messages.company.giftCard.send'));
        } catch (\Exception $exception)
        {
            return $this->error([$exception->getMessage()], trans('messages.company.giftCard.invalid.request'), 422);
        }
    }

    /**
     * @OA\Get (
     *      path="/companies/{company_id}/search/user",
     *      operationId="search user",
     *      tags={"Company"},
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
     *          @OA\JsonContent(ref="/companies/{company_id}/search/user")
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
