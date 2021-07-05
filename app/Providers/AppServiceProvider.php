<?php

namespace App\Providers;

use App\Billing\PaymentGatewayContract;
use App\Billing\PlacetopayGateway;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(ProductRepository $productRepository, OrderRepository $orderRepository)
    {
        $this->app->singleton(PaymentGatewayContract::class, function ($app) use ($productRepository, $orderRepository) {
            $productId = request()->get('product_id');

            if (empty($productId)) {
                $productId = $orderRepository->findOrFail(request()->route('orderId'))->product_id;
            }

            $product = $productRepository->findOrFail($productId);

            return new PlacetopayGateway($product->currency);
        });
    }
}
