<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = ['name', 'subdescription', 'description', 'category_id', 'best_seller', 'price', 'months'];

    protected $casts = [
        'months' => 'array',
    ];

    public function hasOneCategory()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function hasManyImages()
    {
        return $this->hasMany(Album::class);
    }

    public function hasManyData()
    {
        return $this->hasMany(Data::class);
    }

    public function hasManyTags()
    {
        return $this->hasMany(Tag::class);
    }

    public function getFillable()
    {
        return $this->fillable;
    }
}
