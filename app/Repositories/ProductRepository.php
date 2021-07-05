<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\BaseRepository;

class ProductRepository extends BaseRepository
{
    /**
     * {@inheritdoc}
     */
    public function model(): string
    {
        return Product::class;
    }
}
