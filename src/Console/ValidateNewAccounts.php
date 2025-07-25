<?php


namespace Transave\CommonBase\Console;


use Exception;
use Illuminate\Console\Command;
use Transave\CommonBase\Actions\VFD\Account\NewAccountValidation;
use Transave\CommonBase\Helpers\UtilsHelper;

class ValidateNewAccounts extends Command
{
    use UtilsHelper;
    protected $signature = 'transave:validateaccount';
    protected $description = 'validate new unverified users account every 5 minutes';

    public function handle()
    {
        try {
            (new NewAccountValidation)->execute();
            $this->error("loading...");
        } catch (Exception $e) {
            $this->error($e->getTraceAsString());
        }
    }
}
