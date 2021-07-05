<?php

namespace App\Billing;

interface PaymentGatewayContract
{
    /**
     * Charge payment (return init point).
     *
     * @param  string  $reference
     * @param  float   $amount
     * @param  string  $currency
     * @param  array   $options
     * @return object
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function charge(string $reference, float $amount, string $currency, array $options = []): object;
}
