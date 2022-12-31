<?php

namespace App\Http\Controllers\Api\App\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Group\GroupRequest;
use App\Http\Requests\Admin\Group\UpdateGroupRequest;
use App\Http\Resources\Admin\GroupResource;
use App\Http\Resources\Admin\UserResource;
use App\Models\Company;
use App\Models\Group;
use App\Models\User;
use App\Services\Image\ImageService;

class GroupController extends Controller
{

    public function groupUsers(Group $group, Company $company)
    {
        try {
            $company->groups()->findOrFail(['group_id' => $group->id]);
            $users = $group->users;
            return UserResource::collection($users);
        } catch (\Exception $exception)
        {
            return response(['bad request' => $exception->getMessage()], 400);
        }
    }
    public function store(GroupRequest $request, ImageService $imageService, Company $company)
    {
        try
        {
            $attrs = $request->validated();
            if ($request->hasFile('avatar')) {
                $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'groups');
                $result = $imageService->save($request->file('avatar'));
                if ($result === false)
                    return response('error uploading photo ', 400);
                $attrs['avatar'] = $result;
            }
            $attrs['company_id'] = $company->id;
            $group = Group::query()->create($attrs);
            return new GroupResource($group);
        } catch (\Exception $e)
        {
            return response(['errors: ' => $e->getMessage()], 400);
        }
        }


        public function update(UpdateGroupRequest $request, ImageService $imageService, Group $group, Company $company)
        {
            try
            {
                $attrs = $request->validated();
                if ($request->hasFile('avatar')) {
                    if (!empty($group->avatar))
                        $imageService->deleteImage($group->avatar);
                    $imageService->setCustomDirectory('images' . DIRECTORY_SEPARATOR . 'groups');
                    $result = $imageService->save($request->file('avatar'));
                    if ($result === false)
                        return response('error uploading photo ', 400);
                    $attrs['avatar'] = $result;
                }
                $attrs['company_id'] = $company->id;
                $group->update($attrs);
                return GroupResource::make($group);
            } catch (\Exception $e)
            {
                return response(['errors' => $e->getMessage()], 400);
            }
        }

        public function addUser(Group $group, Company $company, User $user)
        {
            try {
                $company->groups()->findOrFail(['group_id' => $group->id]);
                $company->users()->findOrFail(['user_id' => $user->id]);
                $group->users()->sync($user);
                return response('user added', 200);
            } catch (\Exception $exception) {
                return response('error', 400);
            }
      }
    }
