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
    protected $fillable = [
        'user_id',
        'image_url',
        'identity_card_url',
        'address_proof_url',
        'identity_type',
        'identity_card_number',
        'country_of_origin_id',
        'country_of_residence_id',
        'state_id',
        'lga_id',
        'city',
        'next_of_kin',
        'next_of_kin_contact',
        'mother_maiden_name',
        'residential_status',
        'employment_status',
        'employer',
        'job_title',
        'educational_qualification',
        'date_of_employment',
        'number_of_children',
        'income_range',
        'verification_status',
        'is_loan_compliant',
    ];

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