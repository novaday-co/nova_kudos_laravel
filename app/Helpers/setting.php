<?php

use App\Traits\ApiResponser;
function validatePermission($companyPermission)
{
    try {
        $input = 'enable';
        if ($companyPermission !== $input){
            return trans('messages.company.permission.invalid');
        }
        return true;
    } catch (Exception $exception)
    {
        return $exception->getMessage();
    }
}
