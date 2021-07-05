<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Product;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class OrderRepository extends BaseRepository
{
    /**
     * {@inheritdoc}
     */
    public function model(): string
    {
        return Order::class;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $attributes)
    {
        $product = Product::findOrFail($attributes['product_id']);

        $attributes['price'] = $product->price;
        $attributes['currency'] = $product->currency;
        $attributes['status'] = config('app.statuses.default');

        return parent::create($attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function update(Model $model, array $attributes)
    {
        if (array_key_exists('payment_data->status', $attributes)) {
            $paymentStatus = Arr::get($attributes, 'payment_data->status.status');

            $attributes['status'] = $this->getStatusByPaymentStatus($paymentStatus);
        }

        return parent::update($model, $attributes);
    }

    /**
     * Get status by payment status.
     *
     * @param  string  $paymentStatus
     * @return string
     */
    protected function getStatusByPaymentStatus(string $paymentStatus): string
    {
        switch ($paymentStatus) {
            case 'APPROVED':
                return config('app.statuses.payed');

            case 'REJECTED':
                return config('app.statuses.rejected');

            default:
                return config('app.statuses.created');
        }
    }
}
