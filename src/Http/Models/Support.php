<?php


namespace Transave\CommonBase\Http\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Transave\CommonBase\Database\Factories\SupportFactory;
use Transave\CommonBase\Helpers\UuidHelper;

class Support extends Model
{
    use HasFactory, UuidHelper;

    protected $guarded = [ 'id' ];
    protected $table = 'supports';

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory()
    {
        return SupportFactory::new();
    }
}