<?php

namespace Tests\Feature;

use App\Billing\PlacetopayGateway;
use Illuminate\Support\Arr;
use Tests\TestCase;

class PlacetopayGatewayTest extends TestCase
{
    /**
     * Data provider for payment payload.
     *
     * @return array
     */
    public function paymentProvider(): array
    {
        return [
            [
                [
                    'reference' => 'this-is-a-test-reference',
                    'description' => 'This is a test description',
                    'amount' => [
                        'currency' => 'USD',
                        'total' => 100.0,
                    ],
                ],
            ]
        ];
    }

    /**
     * @return \App\Billing\PlacetopayGateway
     */
    public function testAPlacetopayGatewayInstanceCanBeCreated(): PlacetopayGateway
    {
        // create a new gateway instance
        $placetopayGateway = new PlacetopayGateway('usd');

        $this->assertInstanceOf(PlacetopayGateway::class, $placetopayGateway);

        return $placetopayGateway;
    }

    /**
     * @depends testAPlacetopayGatewayInstanceCanBeCreated
     * @dataProvider paymentProvider
     * @return void
     */
    public function testAPlacetopayGatewayFailsWhenChargingAndInvalidPayment(
        array $payment,
        PlacetopayGateway $placetopayGateway
    ): void {
        $reference = Arr::get($payment, 'reference');
        $amount = Arr::get($payment, 'amount.total');
        $currency = 'invalid-currency';
        $options = [
            'description' => Arr::get($payment, 'description')
        ];

        // make Placetopay request
        $paymentResult = $placetopayGateway->charge($reference, $amount, $currency, $options);

        $this->assertNull($paymentResult->requestId);
        $this->assertNull($paymentResult->processUrl);
    }

    /**
     * @depends testAPlacetopayGatewayInstanceCanBeCreated
     * @dataProvider paymentProvider
     * @return void
     */
    public function testAPlacetopayGatewayCanChargeAPaymentSuccessfully(
        array $payment,
        PlacetopayGateway $placetopayGateway
    ): void {
        $reference = Arr::get($payment, 'reference');
        $amount = Arr::get($payment, 'amount.total');
        $currency = Arr::get($payment, 'amount.currency');
        $options = [
            'description' => Arr::get($payment, 'description')
        ];

        // make Placetopay request
        $paymentResult = $placetopayGateway->charge($reference, $amount, $currency, $options);

        $this->assertIsInt($paymentResult->requestId);
        $this->assertIsString($paymentResult->processUrl);
    }
}
