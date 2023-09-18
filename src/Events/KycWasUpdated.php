<?php


namespace Transave\CommonBase\Events;


use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Transave\CommonBase\Http\Models\Kyc;

class KycWasUpdated
{
    use Dispatchable, SerializesModels;

    public $kyc;

    public function __construct(Kyc $kyc)
    {
        $this->kyc = $kyc;
    }
}