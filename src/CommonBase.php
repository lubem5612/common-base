<?php

namespace Transave\CommonBase;



use Transave\CommonBase\Actions\Kuda\Account\CreateVirtualAccount;
use Transave\CommonBase\Actions\Kuda\Account\UpdateVirtualAccount;

class CommonBase
{
    public function createVirtualAccount(array $data)
    {
        return (new CreateVirtualAccount($data))->execute();
    }

    public function updateVirtualAccount(array $data)
    {
        return (new UpdateVirtualAccount($data))->execute();
    }

}