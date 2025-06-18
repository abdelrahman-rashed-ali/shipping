<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    protected $table = 'data';
    protected $fillable = ['product_id', 'name', 'description'];
    //
    public function getFillable()
    {
        return $this->fillable;
    }
}
