<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tags';
    protected $fillable = ['product_id', 'name', 'description'];
    //

    public function getFillable()
    {
        return $this->fillable;
    }
}
