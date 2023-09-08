<?php


namespace Transave\CommonBase\Http\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Transave\CommonBase\Database\Factories\CountryFactory;

class Country extends Model
{
    use HasFactory;

    protected $table = "countries";
    protected $guarded = [ "id" ];

    protected $hidden = ['created_at', 'updated_at'];

    protected static function newFactory()
    {
        return CountryFactory::new();
    }
}