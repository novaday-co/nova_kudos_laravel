<?php

namespace App\Http\Controllers\Api\App\Medal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Medal\MedalRequest;
use App\Http\Requests\Admin\Medal\UpdateMedalRequest;
use App\Http\Resources\Admin\MedalResource;
use App\Http\Services\Image\ImageService;
use App\Models\Medal;
use App\Models\Question;
use App\Models\User;

class MedalController extends Controller
{
    public function index()
    {
        $medals = Medal::query()->latest()->paginate(15);
        return new MedalResource($medals);
    }

    public function store(MedalRequest $request, ImageService $imageService)
    {
        $attrs = $request->validated();
        if ($request->hasFile('icon'))
        {
            $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'medals');
            $result = $imageService->save($request->file('icon'));
            if ($result === false)
                return response('error uploading icon', 400);
            $attrs['icon'] = $result;
        }
        $medal = Medal::query()->create($attrs);
        return new MedalResource($medal);
    }

    public function update(UpdateMedalRequest $request, Medal $medal, ImageService $imageService)
    {
        try {
            $attrs = $request->validated();
            if ($request->hasFile('icon')) {
                if (!empty($medal->icon))
                    $imageService->deleteImage($medal->icon);
                $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'medals');
                $result = $imageService->save($request->file('icon'));
                if ($result === false)
                    return response('error uploading photo ', 400);
                $attrs['icon'] = $result;
            }
            $medal = $medal->update($attrs);
            return new MedalResource($medal);
        } catch (\Exception $e)
        {
            return response(['error' => $e->getMessage()], 400);
        }
    }

    public function medalQuestion(Medal $medal, Question $question)
    {
        try {
            $question->medals()->save($medal);
            return response('added success', 200);
        } catch (\Exception $e)
        {
            return response('bad request', 400);
        }
    }

    public function medalUser(Medal $medal, User $user)
    {
        try {
            $user->medals()->save($medal);
            return response('added success', 200);
        } catch (\Exception $e)
        {
            return response('bad request', 400);
        }
    }
}
