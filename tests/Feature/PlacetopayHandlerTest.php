<?php

namespace Tests\Feature;

use App\Handlers\PlacetopayHandler;
use Exception;
use Illuminate\Support\Arr;
use Tests\TestCase;

class PlacetopayHandlerTest extends TestCase
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
     * @return \App\Handlers\PlacetopayHandler
     */
    public function testAPlacetopayHandlerInstanceCanBeCreated(): PlacetopayHandler
    {
        // create a new handler instance
        $placetopayHandler = new PlacetopayHandler();

        $this->assertInstanceOf(PlacetopayHandler::class, $placetopayHandler);

        return $placetopayHandler;
    }

    /**
     * @depends testAPlacetopayHandlerInstanceCanBeCreated
     * @return void
     */
    public function testAPlacetopayRequestWithoutPaymentSetThrowAnException(PlacetopayHandler $placetopayHandler): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('You should set the payment.');

        // make Placetopay request
        $placetopayHandler->request();
    }

    /**
     * @depends testAPlacetopayHandlerInstanceCanBeCreated
     * @return void
     */
    public function testAPlacetopayRequestWithEmptyPaymentSetFails(PlacetopayHandler $placetopayHandler): void
    {
        // set payment
        $placetopayHandler->setPayment([]);

        // make Placetopay request
        $placetopayHandler->request();

        $this->assertEquals('FAILED', $placetopayHandler->getStatus());
        $this->assertEquals(false, $placetopayHandler->isSuccessful());
    }

    /**
     * @depends testAPlacetopayHandlerInstanceCanBeCreated
     * @dataProvider paymentProvider
     * @return \App\Handlers\PlacetopayHandler
     */
    public function testAPlacetopayRequestWithFilledPaymentRespondsSuccessfully(
        array $payment,
        PlacetopayHandler $placetopayHandler
    ): PlacetopayHandler {
        // set payment
        $placetopayHandler->setPayment($payment);

        // make Placetopay request
        $placetopayHandler->request();

        $this->assertIsArray($placetopayHandler->getContent());
        $this->assertNotEmpty($placetopayHandler->getContent());
        $this->assertEquals('OK', $placetopayHandler->getStatus());
        $this->assertEquals(true, $placetopayHandler->isSuccessful());

        return $placetopayHandler;
    }

    /**
     * @depends testAPlacetopayHandlerInstanceCanBeCreated
     * @dataProvider paymentProvider
     * @return void
     */
    public function testASuccessfulPlacetopayRequestReturnsRequestIdAndProcessUrl(
        array $payment,
        PlacetopayHandler $placetopayHandler
    ): void {
        $placetopayHandler = $this->testAPlacetopayRequestWithFilledPaymentRespondsSuccessfully(
            $payment,
            $placetopayHandler
        );

        $this->assertIsInt($placetopayHandler->getRequestId());
        $this->assertIsString($placetopayHandler->getProcessUrl());
    }

    /**
     * @depends testAPlacetopayHandlerInstanceCanBeCreated
     * @dataProvider paymentProvider
     * @return void
     */
    public function testASuccessfulPlacetopayRequestInfoCanBeRequestedAsPending(
        array $payment,
        PlacetopayHandler $placetopayHandler
    ): void {
        $placetopayHandler = $this->testAPlacetopayRequestWithFilledPaymentRespondsSuccessfully(
            $payment,
            $placetopayHandler
        );

        $placetopayHandler->getRequestInfo($placetopayHandler->getRequestId());

        $this->assertEquals('PENDING', $placetopayHandler->getStatus());
        $this->assertEquals(
            route('orders.show', ['orderId' => Arr::get($payment, 'reference')]),
            Arr::get($placetopayHandler->getContent(), 'request.returnUrl')
        );
    }
}
