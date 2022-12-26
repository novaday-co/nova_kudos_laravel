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

    /**
     * @OA\Post (
     *      path="/api/app/events/companies/{company}",
     *      operationId="store new event",
     *      tags={"events"},
     *      summary="store new event",
     *      description="store new event",
     *      security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *          name="company",
     *          in="path",
     *          required=true,
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
     *                  @OA\Property(property="title", type="text", format="text", example="yasin"),
     *                   required={"title"},
     *                  @OA\Property(property="description", type="text", format="text", example="long text"),
     *                  @OA\Property(property="price", type="integer", format="integer", example="23"),
     *                   required={"price"},
     *                  @OA\Property(property="avatar", type="file", format="text"),
     *                  @OA\Property(property="expiration_date", type="text", format="text", example="23/12/22"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/api/app/events/companies/{company}")
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
    /**
     * @OA\Put(
     *      path="/api/app/events/{event}/update",
     *      operationId="update event",
     *      tags={"events"},
     *      summary="update event",
     *      description="update event",
     *      security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *          name="event",
     *          in="path",
     *          required=true,
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
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  required={"name"},
     *                  @OA\Property(property="name", type="text", format="text", example="yasin"),
     *                  @OA\Property(property="description", type="text", format="text", example="long text"),
     *                  @OA\Property(property="price", type="integer", format="integer", example="23"),
     *                   required={"price"},
     *                  @OA\Property(property="avatar", type="file", format="text"),
     *                  @OA\Property(property="expiration_date", type="text", format="text", example="23/12/22"),
     *               ),
     *           ),
     *       ),
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *     @OA\JsonContent(ref="/api/app/events/{event}/update")
     *       ),
     *     @OA\Response(
     *          response=401,
     *          description="validation error",
     *      ),
     *     @OA\Response(
     *          response=400,
     *          description="error",
     *       ),
     *     @OA\Response(
     *          response=500,
     *          description="server error",
     *      ),
     * )
     */
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

    /**
     * @OA\Post (
     *      path="/api/app/events/{event}/users/{user}",
     *      operationId="add user to view event",
     *      tags={"events"},
     *      summary="add user to view event",
     *      description="add user to view eventt",
     *      security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *          name="event",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="user",
     *          in="path",
     *          required=true,
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
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/api/app/events/{event}/users/{user}")
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

    /**
     * @OA\Post (
     *      path="/api/app/events/{event}/groups/{group}",
     *      operationId="add group to view event",
     *      tags={"events"},
     *      summary="add group to view event",
     *      description="add group to view eventt",
     *      security={ {"sanctum": {} }},
     *     @OA\Parameter(
     *          name="event",
     *          in="path",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="user",
     *          in="path",
     *          required=true,
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
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *          @OA\JsonContent(ref="/api/app/events/{event}/groups/{group}")
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
