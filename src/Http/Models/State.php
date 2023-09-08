<?php


namespace Transave\CommonBase\Http\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Transave\CommonBase\Database\Factories\StateFactory;

class State extends Model
{
    use HasFactory;

    protected $table = "states";
    protected $guarded = [ "id" ];

    protected $hidden = ['created_at', 'updated_at'];

    public function country() : BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    protected static function newFactory()
    {
        return StateFactory::new();
    }
}