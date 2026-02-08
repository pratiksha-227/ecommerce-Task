<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id', 'product_id', 'product_image_id', 'quantity', 'price', 'total'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function selectedImage()
    {
        return $this->belongsTo(ProductImage::class, 'product_image_id');
    }
}
