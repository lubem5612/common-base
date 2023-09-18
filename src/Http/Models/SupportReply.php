<?php


namespace Transave\CommonBase\Http\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Transave\CommonBase\Database\Factories\SupportReplyFactory;
use Transave\CommonBase\Helpers\UuidHelper;

class SupportReply extends Model
{
    use HasFactory, UuidHelper;

    protected $guarded = [ 'id' ];
    protected $table = 'support_replies';

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function support() : BelongsTo
    {
        return $this->belongsTo(Support::class);
    }

    protected static function newFactory()
    {
        return SupportReplyFactory::new();
    }
}