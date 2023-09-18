<?php


namespace Transave\CommonBase\Http\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Transave\CommonBase\Database\Factories\DebitCardFactory;
use Transave\CommonBase\Helpers\UuidHelper;

class DebitCard extends Model
{
    use HasFactory, UuidHelper;

    protected $table = "debit_cards";
    protected $guarded = [
        "id"
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory()
    {
        return DebitCardFactory::new();
    }
}