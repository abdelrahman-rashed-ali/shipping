<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = ['name', 'description', 'image'];

    public function hasManyProducts(): HasMany
    {
        return $this->hasMany('App\Models\Product', 'category_id');
    }

    public function getFillable()
    {
        return $this->fillable;
    }
    //
}
