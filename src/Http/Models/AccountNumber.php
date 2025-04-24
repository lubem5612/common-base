<?php

namespace Transave\CommonBase\Http\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Transave\CommonBase\Database\Factories\AccountNumberFactory;
use Transave\CommonBase\Helpers\UuidHelper;

class AccountNumber extends Model
{
    use HasFactory, UuidHelper;

    protected $table = 'account_numbers';

    protected $fillable = ['user_id', 'account_number', 'bank_name'];

    protected static function newFactory()
    {
        return AccountNumberFactory::new();
    }
}
