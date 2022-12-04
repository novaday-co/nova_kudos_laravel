<?php

namespace App\Http\Controllers\Api\App\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Profile\ProfileRequest;
use App\Http\Resources\Admin\UserResource;
use App\Http\Services\Image\ImageService;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(ProfileRequest $request, User $user, ImageService $imageService)
    {
        try
        {
            $attrs = $request->validated();
            if ($request->hasFile('avatar')) {
                if (!empty($user->avatar))
                    $imageService->deleteImage($user->avatar);
                $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'users');
                $result = $imageService->save($request->file('avatar'));
                if ($result === false)
                    return response('error uploading photo ', 400);
                $attrs['avatar'] = $result;
            }
            $user = $user->update($attrs);
            return UserResource::make($user);
        } catch (\Exception $e)
        {
            return response(['bad request' =>$e->getMessage()], 400);
        }
    }
}
