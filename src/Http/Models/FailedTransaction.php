<?php


namespace Transave\CommonBase\Http\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Transave\CommonBase\Database\Factories\FailedTransactionFactory;
use Transave\CommonBase\Helpers\UuidHelper;

class FailedTransaction extends Model
{
    use HasFactory, UuidHelper;

    protected $table = "failed_transactions";
    protected $guarded = [
        "id"
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory()
    {
        return FailedTransactionFactory::new();
    }
}