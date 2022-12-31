<?php

namespace App\Http\Controllers\Api\App\Event;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Event\EventRequest;
use App\Http\Requests\Admin\Event\UpdateEventRequest;
use App\Http\Resources\Admin\EventResource;
use App\Models\Company;
use App\Models\Event;
use App\Models\Group;
use App\Models\User;
use App\Services\Image\ImageService;

class EventController extends Controller
{

    public function store(EventRequest $request, Company $company, ImageService $imageService)
    {
        try
        {
            $attrs = $request->validated();
            if ($request->hasFile('avatar')) {
               $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'events');
               $result = $imageService->save($request->file('avatar'));
                if ($result === false)
                    return response('error uploading photo ', 400);
                $attrs['avatar'] = $result;
            }
            $attrs['company_id'] = $company->id;
            $event = Event::query()->create($attrs);
            return new EventResource($event);
        } catch (\Exception $e)
        {
            return response(['errors' => $e->getMessage()], 400);
        }
    }
    public function update(UpdateEventRequest $request, Event $event, ImageService $imageService)
    {
        try
        {
            // validation
            $attrs = $request->validated();
            // check request for upload image
            if ($request->hasFile('avatar')) {
                // check image exists or not
                if (!empty($event->avatar))
                    $imageService->deleteImage($event->avatar);
                $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'events');
                $result = $imageService->save($request->file('avatar'));
                // check upload
                if ($result === false)
                    return response('error uploading photo ', 400);
                $attrs['avatar'] = $result;
            }
            $event = Event::query()->update($attrs);
            return new EventResource($event);
        }catch (\Exception $exception)
        {
            return response(['error' =>$exception->getMessage()], 400);
        }
    }

  /*  public function destroy(Event $event)
    {
        $event->delete();
        return response('event deleted success');
    }*/
    public function userType(Event $event, User $user)
    {
        try
        {
            $user->eventTypes()->attach($event);
            return response('added', 200);
        } catch (\Exception $exception)
        {
            return response(['error:' => $exception->getMessage()], 400);
        }
    }

    public function groupType(Event $event, Group $group)
    {
        try {
            $group->eventTypes()->attach($event);
            return response('added', 200);
        } catch (\Exception $exception) {
            return response(['error:' => $exception->getMessage()], 400);
        }
    }

    public function participateUser(Event $event, User $user)
    {
        try {
            $event->users()->attach($user);
            return response('added', 200);
        } catch (\Exception $exception)
        {
            return response(['error:' => $exception->getMessage()], 400);
        }
    }
}
