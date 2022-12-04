<?php

namespace App\Http\Controllers\Api\App\GiftCard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GiftCard\SendGiftRequest;
use App\Http\Resources\Admin\SendGiftResource;
use App\Models\GiftCard;
use App\Models\GiftUser;
use App\Models\User;
use Illuminate\Http\Request;

class SendGiftCardController extends Controller
{
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
