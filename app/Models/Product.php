<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Product extends Model
{
    protected $fillable = [
        'image',
        'title',
        'description',
        'price',
        'stock'
    ];

    // accesor image
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => url('/storage/products/' . $image),
        );
    }
}
