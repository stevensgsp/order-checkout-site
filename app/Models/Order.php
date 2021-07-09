<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Order extends Model
{
    /**
     * {@inheritdoc}
     */
    protected static $unguarded = true;

    /**
     * {@inheritdoc}
     */
    protected $casts = [
        'payment_data' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Determines if the order has the given status(es).
     *
     * @return string|array
     */
    public function hasStatus($status)
    {
        return in_array($this->status, (array) $status);
    }

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

    /**
     * Get the payment status.
     *
     * @return string|null
     */
    public function getPaymentStatusAttribute(): ?string
    {
        return Arr::get($this->payment_data, 'status.status');
    }

    /**
     * Get the payment status message.
     *
     * @return string|null
     */
    public function getPaymentStatusMessageAttribute(): ?string
    {
        return Arr::get($this->payment_data, 'status.message');
    }

    /**
     * Get the process url.
     *
     * @return string|null
     */
    public function getProcessUrlAttribute(): ?string
    {
        return Arr::get($this->payment_data, 'processUrl');
    }

    /**
     * Get the payment status html class.
     *
     * @codeCoverageIgnore
     *
     * @return string|null
     */
    public function getPaymentStatusClassAttribute(): ?string
    {
        switch ($this->getPaymentStatusAttribute()) {
            case 'APPROVED':
                return 'success';

            case 'REJECTED':
                return 'danger';

            default:
                return 'warning';
        };
    }

    /**
     * Get the status html class.
     *
     * @codeCoverageIgnore
     *
     * @return string|null
     */
    public function getStatusClassAttribute(): ?string
    {
        switch ($this->status) {
            case config('app.statuses.payed'):
                return 'success';

            case config('app.statuses.rejected'):
                return 'danger';

            default:
                return 'secondary';
        };
    }
}
