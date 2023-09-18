<?php


namespace Transave\CommonBase\Http\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Transave\CommonBase\Actions\Kuda\Account\MainAccountBalance;
use Transave\CommonBase\Actions\Kuda\Account\VirtualAccountBalance;
use Transave\CommonBase\Database\Factories\UserFactory;
use Transave\CommonBase\Helpers\KycHelper;
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
        'account_verified_at'
    ];

    protected $casts = [
        'account_verified_at' => 'datetime',
    ];

    protected $appends = [
        'wallet', 'account'
    ];

    public function kyc() : HasOne
    {
        return $this->hasOne(Kyc::class);
    }

    public function transactions() : HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getWalletAttribute()
    {
        if ($this->role=='admin') {
            $response = (new MainAccountBalance())->execute();
            if ($response['success']) {
                return $response['data'];
            }
        }else {
            $response = (new VirtualAccountBalance(['user_id' => $this->id]))->execute();
            if ($response['success']) {
                return $response['data'];
            }
        }
        return null;
    }

    public function getAccountAttribute()
    {
        $response = (new KycHelper(['user_id' => $this->id]))->execute();
        if ($response['success']){
            return $response['data'];
        }
        return null;
    }

    public function debitCards() : HasMany
    {
        return $this->hasMany(DebitCard::class, 'user_id', 'id');
    }

    public function supports() : HasMany
    {
        return $this->hasMany(Support::class);
    }

    public function supportReplies() : HasMany
    {
        return $this->hasMany(SupportReply::class, 'user_id', 'id');
    }

    public function failedTransactions() : HasMany
    {
        return $this->hasMany(FailedTransaction::class, 'user_id', 'id');
    }

    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
