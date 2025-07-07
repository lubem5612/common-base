<?php

namespace Transave\CommonBase\Http\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Transave\CommonBase\Database\Factories\WalletFactory;
use Transave\CommonBase\Helpers\UuidHelper;

class Wallet extends Model
{
    use HasFactory, UuidHelper;

    protected $table = 'wallets';

    protected $fillable = ['user_id', 'balance'];

    protected static function newFactory()
    {
        return WalletFactory::new();
    }
}
