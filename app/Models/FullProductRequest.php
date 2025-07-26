<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FullProductRequest extends Model
{
    protected $table = 'full_product_requests';
    protected $guarded = [];
    //
    protected $fillable = [
        'full_name',
        'company_name',
        'email',
        'phone',
        'product_type',
        'product_shape',
        'packaging_type',
        'pacage_weight',
        'quantity',
        'ship_to',
        'shipping_method',
        'additional_message',
    ];
    public function getFillable()
    {
        return $this->fillable;
    }
}
