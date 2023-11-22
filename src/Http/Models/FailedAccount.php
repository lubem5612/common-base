<?php


namespace Transave\CommonBase\Http\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Transave\CommonBase\Database\Factories\FailedAccountFactory;

class FailedAccount extends Model
{
    use HasFactory;

    protected $table = "failed_accounts";
    protected $guarded = [
        "id"
    ];

    protected static function newFactory()
    {
        return FailedAccountFactory::new();
    }
}