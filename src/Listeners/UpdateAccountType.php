<?php


namespace Transave\CommonBase\Listeners;


use Transave\CommonBase\Events\KycWasUpdated;

class UpdateAccountType
{
    public function handle(KycWasUpdated $event)
    {
        $event->kyc->user()->update([
            'account_type' => 'classic'
        ]);
    }
}