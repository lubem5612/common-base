<?php


namespace Transave\CommonBase\Helpers;


use Illuminate\Support\Facades\Cache;

trait SessionHelper
{

    protected function incrementSession($user_id) : void
    {
        Cache::store('file')->add($user_id, 0, 120);
        Cache::store('file')->increment($user_id);
    }

    protected function getSession($user_id) : int
    {
        return Cache::store('file')->get($user_id);
    }

}