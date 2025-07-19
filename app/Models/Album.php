<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $table = 'album';
    protected $fillable = ['product_id', 'image', 'is_main'];
    //
    public function getFillable()
    {
        return $this->fillable;
    }
}
