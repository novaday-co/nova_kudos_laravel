<?php

namespace App\Http\Controllers\Api\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Setting\SettingRequest;
use App\Http\Resources\Admin\SettingResource;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{

    public function valueOfCoin()
    {
        $valueCoin = Setting::query()->first();
        return new SettingResource($valueCoin);
    }

    public function valueCoin(SettingRequest $request)
    {
        try
        {
            // validation
            $attrs = $request->validated();
            $setting = Setting::query()->updateOrCreate([
               'key' => $attrs['key'],
               'value' => $attrs['key']
            ],[
                'key' => $attrs['key'],
                'value' => $attrs['value']
            ]);
            return new SettingResource($setting);
        } catch (\Exception $e)
        {
            return response(['bad request' => $e->getMessage()], 400);
        }
    }
}
