<?php


namespace Transave\CommonBase\Http\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Transave\CommonBase\Database\Factories\UserFactory;
use Transave\CommonBase\Helpers\UuidHelper;

class User extends Authenticatable
{
    use HasFactory, Notifiable, UuidHelper, HasApiTokens;
    protected $table = "users";

    protected $guarded = [
        "id"
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'transaction_pin',
        'bvn',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function wallet() : HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function kyc() : HasOne
    {
        return $this->hasOne(Kyc::class);
    }

    public function transactions() : HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    protected static function newFactory()
    {
        return UserFactory::new();
    }
}