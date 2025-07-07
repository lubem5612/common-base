<?php


namespace Transave\CommonBase\Helpers;


use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

trait SessionHelper
{

    protected function incrementSession($user_id) : void
    {
        Cache::store('file')->add($user_id, 0, 120);
        Cache::store('file')->increment($user_id);
    }

    protected function getSession($user_id)
    {
        return Cache::store('file')->get($user_id);
    }

    protected function generateReference()
    {
        return 'transave-'. Carbon::now()->format('YmdHi') . mt_rand(123456, 999999);
    }

}