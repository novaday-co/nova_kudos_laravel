<?php

use App\Traits\ApiResponser;
function validatePermission($companyPermission)
{
    try {
        if ($companyPermission !== 'enable'){
            return response()->json([trans('messages.company.setting.permission.invalid')]);
        }
    } catch (Exception $exception)
    {
        return $exception->getMessage();
    }
}
