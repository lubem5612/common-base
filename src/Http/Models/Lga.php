<?php


namespace Transave\CommonBase\Http\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Transave\CommonBase\Database\Factories\LgaFactory;

class Lga extends Model
{
    use HasFactory;

    protected $table = "lgas";
    protected $guarded = [ "id" ];

    protected $hidden = ['created_at', 'updated_at'];

    public function state() : BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    protected static function newFactory()
    {
        return LgaFactory::new();
    }
}