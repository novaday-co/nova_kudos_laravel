<?php

namespace App\Http\Controllers\Api\App\Company\GiftCard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GiftCard\SendGiftRequest;
use App\Http\Resources\Admin\SendGiftResource;
use App\Models\GiftCard;
use App\Models\GiftUser;
use App\Models\User;

class SendGiftCardController extends Controller
{
    /**
     * @OA\Post (
     *      path="/api/giftcards/{giftcard}/companies/{company}/users/{to_user}",
     *      operationId="send gift card",
     *      tags={"giftcards"},
     *      summary="send gift card",
     *      description="send gift card",
     *      security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *         name="giftcard",
     *         in="path",
     *         required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *     @OA\Parameter(
     *         name="company",
     *         in="path",
     *         required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *         @OA\Parameter(
     *         name="to_user",
     *         in="path",
     *         required=true,
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
     *                  @OA\Property(property="message", type="text", format="text", example="yasin"),
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
    public function sendTo(SendGiftRequest $request, User $from_id, User $to_id, GiftCard $gift_id)
    {
       // try {
            $attrs = $request->validated();
            $gift = GiftUser::query()->create([
                'from_id' => $from_id->id,
                'to_id' => $to_id->id,
                'gift_id' => $gift_id->id,
                'message' => $attrs['message']
            ]);
            return new SendGiftResource($gift);
        //} catch (\Exception $e)
       // {
         //   return response(['bad request' => $e->getMessage()],400);
        //}

    }
}
