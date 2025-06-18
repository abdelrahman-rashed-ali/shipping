<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class album extends Model
{
    protected $table = 'album';
    protected $fillable = ['product_id', 'image', 'is_main'];
    //
    public function getFillable()
    {
        return $this->fillable;
    }
}
