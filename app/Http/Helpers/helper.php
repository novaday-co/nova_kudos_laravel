<?php

use Morilog\Jalali\Jalalian;

/**
 * persian date time
 * @param string $date
 * @return string
 */
function dateTime(string $date): string
{
    $setTime = Jalalian::forge($date)->format('%B %d، %Y');
    return $setTime;
}

/**
 * exchange status format
 * @param $status
 * @return string
 */
function exchangeStatus($status): string
{
    switch ($status)
    {
        case('pending'):
            $status = 'در انتظار تایید';
            break;
        case('failed') :
            $status = 'تایید نشده';
            break;
        case('done') :
            $status = 'انجام شده';
    }
    return $status;
}

/**
 * exchange transaction type
 * @param $type
 * @return string
 */
function exchangeTransactionType($type): string
{
    switch ($type)
    {
        case ('withdrawal'):
            $type = 'برداشت';
            break;
        case ('deposit'):
            $type = 'واریز';
    }
    return $type;
}

function boolType($type)
{
    switch ($type)
    {
        case (0):
            $type = false;
            break;
        case (1):
            $type = true;
    }
    return $type;
}
