<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyRequest extends Model
{
    protected $table = 'company_requests';
    protected $guarded = [];
    protected $fillable = ['email','company','ship_to'];
    public function getFillable()
    {
        return $this->fillable;
    }
}
