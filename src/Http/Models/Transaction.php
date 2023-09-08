<?php


namespace Transave\CommonBase\Http\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Transave\CommonBase\Database\Factories\TransactionFactory;
use Transave\CommonBase\Helpers\UuidHelper;

class Transaction extends Model
{
    use HasFactory, UuidHelper;
    protected $guarded = [ "id" ];
    protected $table = "transactions";

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory()
    {
        return TransactionFactory::new();
    }
}