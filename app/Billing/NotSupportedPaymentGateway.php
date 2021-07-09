<?php

namespace App\Billing;

class NotSupportedPaymentGateway implements PaymentGatewayContract
{
    /**
     * Not supported.
     *
     * @codeCoverageIgnore
     *
     * @param  string  $reference
     * @param  float   $amount
     * @param  string  $currency
     * @param  array   $options
     * @return object
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function charge(string $reference, float $amount, string $currency, array $options = []): object
    {
        abort(400, __('Payment gateway not supported.'));
    }
}
