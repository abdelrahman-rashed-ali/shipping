<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRequest extends Model
{
    protected $table = 'product_requests';
    protected $guarded = [];
    //
    protected $fillable = ['email', 'product', 'message'];

    public function getFillable()
    {
        return $this->fillable;
    }
}
