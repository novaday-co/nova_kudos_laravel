<?php

namespace App\Http\Controllers\Api\App\Medal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Medal\MedalRequest;
use App\Http\Requests\Admin\Medal\UpdateMedalRequest;
use App\Http\Resources\Admin\MedalResource;
use App\Http\Services\Image\ImageService;
use App\Models\Medal;
use App\Models\Question;

class MedalController extends Controller
{
    public function index()
    {
        $medals = Medal::query()->latest()->paginate(15);
        return new MedalResource($medals);
    }

    public function store(MedalRequest $request, Question $question, ImageService $imageService)
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
        $attrs['question_id'] = $question->id;
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
}
