<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }  
    
    public function brand()
    {
        return $this->belongsTo(Brand::class,'brand_id');
    }

    public function getDiscountPercentage()
    {
        if ($this->regular_price > 0 && $this->sale_price < $this->regular_price) {
            return round((($this->regular_price - $this->sale_price) / $this->regular_price) * 100);
        }
        return 0;
    }
}
