<?php


namespace Transave\CommonBase\Http\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Transave\CommonBase\Database\Factories\WalletFactory;
use Transave\CommonBase\Helpers\UuidHelper;

class Wallet extends Model
{
    use HasFactory, UuidHelper;

    protected $table = "wallets";
    protected $guarded = [ "id" ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory()
    {
        return WalletFactory::new();
    }
}