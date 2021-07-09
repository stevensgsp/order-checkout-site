<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Testing\Assert as PHPUnit;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Determine if the seed task should be run when refreshing the database.
     *
     * @var bool
     */
    protected $seed = true;

    /**
     * @return void
     */
    public function testTheShowStoreRouteSuccessfullyResponds(): void
    {
        $response = $this->get(route('orders.show-store'));

        $response->assertStatus(200);
        $response->assertViewIs('orders.show-store');
    }

    /**
     * @return void
     */
    public function testTheCheckoutRouteSuccessfullyResponds(): void
    {
        $productId = Product::firstOrFail(['id'])->id;

        $response = $this->get(route('orders.create', compact('productId')));

        $response->assertStatus(200);
        $response->assertViewIs('orders.create');
    }

    /**
     * @return void
     */
    public function testTheProcessRouteSuccessfullyCreatesAnOrder(): array
    {
        $product = Product::firstOrFail();

        $request = [
            'customer_name'   => 'Steven Sucre',
            'customer_email'  => 'steven.gsp@gmail.com',
            'customer_mobile' => '+584241451008',
            'product_id'      => $product->id,
        ];

        $response = $this->post(route('orders.process'), $request);

        $this->assertCount(1, Order::get());
        $order = Order::firstOrFail();

        $this->assertEquals($request['customer_name'], $order->customer_name);
        $this->assertEquals($request['customer_email'], $order->customer_email);
        $this->assertEquals($request['customer_mobile'], $order->customer_mobile);
        $this->assertEquals(config('app.statuses.default'), $order->status);
        $this->assertEquals($request['product_id'], $order->product_id);
        $this->assertEquals($product->price, $order->price);
        $this->assertEquals($product->currency, $order->currency);
        $this->assertIsInt(Arr::get($order->payment_data, 'requestId'));
        $this->assertIsString(Arr::get($order->payment_data, 'processUrl'));

        return [$response, $order];
    }

    /**
     * @depends testTheProcessRouteSuccessfullyCreatesAnOrder
     * @return void
     */
    public function testTheProcessRouteSuccessfullyRedirectsToThePlacetopayProcessUrl(array $data): void
    {
        [$processResponse, $order] = $data;

        $processResponse->assertStatus(302);
        $processResponse->assertRedirect(Arr::get($order->payment_data, 'processUrl'));
    }

    /**
     * @return void
     */
    public function testTheIndexRouteSuccessfullyResponds(): void
    {
        $response = $this->get(route('orders.index'));

        $response->assertStatus(200);
        $response->assertViewIs('orders.index');
    }

    /**
     * @depends testTheProcessRouteSuccessfullyCreatesAnOrder
     * @return void
     */
    public function testTheShowRouteSuccessfullyResponds(array $data): void
    {
        [, $order] = $data;

        // we save the order because it is not, due to laravel resets the database after each of the tests
        Order::create($order->toArray());

        $response = $this->get(route('orders.show', ['orderId' => $order->id]));

        $response->assertStatus(200);
        $response->assertViewIs('orders.index');

        // here we check that the order in the view is the same that the created order
        $this->assertTrue($order->is(Arr::first($response->viewData('orders'))));
    }

    /**
     * @depends testTheProcessRouteSuccessfullyCreatesAnOrder
     * @return void
     */
    public function testTheReprocessRouteSuccessfullyReprocessAnOrderInCreatedStatus(array $data): void
    {
        [, $order] = $data;

        // we save the order because it is not, due to laravel resets the database after each of the tests
        Order::create($order->toArray());

        $this->assertEquals(config('app.statuses.created'), $order->status);

        $response = $this->get(route('orders.reprocess', ['orderId' => $order->id]));

        $response->assertStatus(302);
        $response->assertRedirect(Arr::get($order->payment_data, 'processUrl'));
    }

    /**
     * @depends testTheProcessRouteSuccessfullyCreatesAnOrder
     * @return void
     */
    public function testTheReprocessRouteDoesNotReprocessAnOrderInPayedStatus(array $data): void
    {
        [, $order] = $data;

        // change the status for the test
        $order->status = config('app.statuses.payed');

        // we save the order because it is not, due to laravel resets the database after each of the tests
        Order::create($order->toArray());

        $this->assertEquals(config('app.statuses.payed'), $order->status);

        $response = $this->get(route('orders.reprocess', ['orderId' => $order->id]));

        $response->assertStatus(302);
        $response->assertRedirect(route('orders.show', ['orderId' => $order->id]));
    }

    /**
     * @depends testTheProcessRouteSuccessfullyCreatesAnOrder
     * @return void
     */
    public function testTheReprocessRouteSuccessfullyReprocessAnOrderInRejectedStatus(array $data): void
    {
        [, $order] = $data;

        // change the status for the test
        $order->status = config('app.statuses.rejected');

        // we save the order because it is not, due to laravel resets the database after each of the tests
        Order::create($order->toArray());

        $this->assertEquals(config('app.statuses.rejected'), $order->status);

        $response = $this->get(route('orders.reprocess', ['orderId' => $order->id]));

        $response->assertStatus(302);

        // here we check that the redirected url not matches the previous process url (because it needs to be a new one)
        $this->assertNotEquals(
            app('url')->to($response->headers->get('Location')),
            Arr::get($order->payment_data, 'processUrl')
        );
    }
}
