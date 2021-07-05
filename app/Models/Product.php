<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /**
     * {@inheritdoc}
     */
    protected static $unguarded = true;

    /**
     * Get the real price.
     *
     * @return string
     */
    public function getRealPriceAttribute()
    {
        $realPrice = $this->price / 100;

        return number_format($realPrice, 2);
    }
}
