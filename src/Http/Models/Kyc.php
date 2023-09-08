<?php


namespace Transave\CommonBase\Http\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Transave\CommonBase\Database\Factories\KycFactory;
use Transave\CommonBase\Helpers\UuidHelper;

class Kyc extends Model
{
    use HasFactory, UuidHelper;

    protected $table = "kycs";
    protected $guarded = [ "id" ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function residence() : BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_of_residence_id', 'id');
    }

    public function origin() : BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_of_origin_id', 'id');
    }

    public function state() : BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id', 'id');
    }

    public function lga() : BelongsTo
    {
        return $this->belongsTo(Lga::class, 'lga_id', 'id');
    }

    protected static function newFactory()
    {
        return KycFactory::new();
    }
}