<?php

namespace App\Billing;

use App\Handlers\PlacetopayHandler;
use Exception;
use Illuminate\Support\Facades\Log;

class PlacetopayGateway implements PaymentGatewayContract
{
    /**
     * @var string
     */
    private $currency;

    /**
     * @var \App\Handlers\PlacetopayHandler
     */
    private $placetopay;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct($currency)
    {
        $this->currency = strtoupper($currency);
        $this->placetopay = new PlacetopayHandler();
    }

    /**
     * Charge payment with Placetopay.
     *
     * @param  string  $reference
     * @param  float   $amount
     * @param  string  $currency
     * @param  array   $options {
     *     Configuration options.
     *
     *     @type string $description [optional]
     * }
     * @return object
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function charge(string $reference, float $amount, string $currency, array $options = []): object
    {
        $opt['description'] = '';

        foreach ($options as $optionName => $o) {
            $opt[$optionName] = $o;
        }

        // set payment
        $this->placetopay->setPayment([
            'reference' => $reference,
            'description' => $opt['description'],
            'amount' => [
                'currency' => $currency,
                'total' => $amount,
            ],
        ]);

        // make Placetopay request
        $this->placetopay->request();

        // check if request was successful
        if ($this->placetopay->isSuccessful()) {
            $requestId = $this->placetopay->getRequestId();
            $processUrl = $this->placetopay->getProcessUrl();
        } else {
            $requestId = null;
            $processUrl = null;
        }

        return (object) compact('requestId', 'processUrl');
    }
}
