<?php

namespace App\Http\Controllers\Api\App\GiftCard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GiftCard\GiftCardRequest;
use App\Http\Requests\Admin\GiftCard\UpdateGiftCardRequest;
use App\Http\Resources\Admin\GiftCardResource;
use App\Http\Services\Image\ImageService;
use App\Models\GiftCard;

class GiftCardController extends Controller
{
    public function index()
    {
        $giftCards = GiftCard::query()->latest()->paginate(15);
        return new GiftCardResource($giftCards);
    }

    public function store(GiftCardRequest $request, ImageService $imageService)
    {
        try
        {
            // validation request
            $attrs = $request->validated();
            if ($request->hasFile('picture'))
            {
                $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'giftcards');
                $result = $imageService->save($request->file('picture'));
                // check upload
                if ($result === false)
                    return response('error uploading photo ', 400);
                $attrs['picture'] = $result;
            }
            $giftCard = GiftCard::query()->create($attrs);
            return new GiftCardResource($giftCard);
        } catch (\Exception $e)
        {
            return response(['bad request' => $e->getMessage()], 400);
        }
    }

    public function update(UpdateGiftCardRequest $request, GiftCard $giftCard, ImageService $imageService)
    {
        try
        {
            // validation request
            $attrs = $request->validated();
            // check request for upload image
            if ($request->hasFile('picture'))
            {
                // check image exists or not
                if (!empty($giftCard->picture))
                    $imageService->deleteImage($giftCard->picture);
                $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'giftcards');
                $result = $imageService->save($request->file('picture'));
                // check upload
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

    public function destroy(GiftCard $giftCard)
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
    }
}
