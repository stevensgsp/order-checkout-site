<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGatewayContract;
use App\Handlers\PlacetopayHandler;
use App\Http\Requests\ProcessOrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(OrderRepository $orderRepo, ProductRepository $productRepo)
    {
        $this->orderRepository = $orderRepo;
        $this->productRepository = $productRepo;
    }

    /**
     * Show the store view.
     *
     * @return \Illuminate\Http\Response
     */
    public function showStore()
    {
        // get all products
        $products = $this->productRepository->all();

        return view('orders.show-store', compact('products'));
    }

    /**
     * Show the form for creating a new order.
     *
     * @param  string  $productId
     * @return \Illuminate\Http\Response
     */
    public function create($productId)
    {
        // get the product
        $product = $this->productRepository->findOrFail($productId);

        return view('orders.create', compact('product'));
    }

    /**
     * Process the order.
     *
     * @param  \App\Http\Requests\ProcessOrderRequest  $request
     * @param  \App\Billing\PaymentGatewayContract  $paymentGateway
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function process(ProcessOrderRequest $request, PaymentGatewayContract $paymentGateway)
    {
        // input request
        $input = $request->validated();

        // create the order
        $order = $this->orderRepository->create($input);

        // make request to payment gateway
        return $this->chargePayment($order, $paymentGateway);
    }

    /**
     * Reprocess the order.
     *
     * @param  string  $orderId
     * @param  \App\Billing\PaymentGatewayContract  $paymentGateway
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function reprocess($orderId, PaymentGatewayContract $paymentGateway)
    {
        // get the order
        $order = $this->orderRepository->findOrFail($orderId);

        // redirect to order detail if already payed
        if ($order->hasStatus(config('app.statuses.payed'))) {
            return redirect(route('orders.show', compact('orderId')));
        }

        // make request to payment gateway if previous payment was rejected
        if ($order->hasStatus(config('app.statuses.rejected'))) {
            return $this->chargePayment($order, $paymentGateway);
        }

        return redirect($order->process_url);
    }

    /**
     * Charge payment.
     *
     * @param  \App\Models\Order  $order
     * @param  \App\Billing\PaymentGatewayContract  $paymentGateway
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    protected function chargePayment(Order $order, PaymentGatewayContract $paymentGateway)
    {
        // charge the order (via placetopay)
        $paymentResult = $paymentGateway->charge(
            $order->id,
            (float) $order->real_price,
            $order->currency,
            ['description' => $order->product->name]
        );

        // update the order with payment data
        $this->orderRepository->update($order, [
            'payment_data->requestId' => $paymentResult->requestId,
            'payment_data->processUrl' => $paymentResult->processUrl,
        ]);

        // redirect to the processUrl
        return redirect($paymentResult->processUrl);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get all orders
        $orders = $this->orderRepository->all();

        return view('orders.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $orderId
     * @return \Illuminate\Http\Response
     */
    public function show($orderId)
    {
        // get the order
        $order = $this->orderRepository->findOrFail($orderId);

        // check if the order is already payed
        $this->checkPaymentStatus($order);

        return view('orders.index', ['orders' => [$order]]);
    }

    /**
     * Check if the order is not payed and update its status.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    protected function checkPaymentStatus(Order $order): void
    {
        if (! $order->hasStatus(config('app.statuses.payed'))) {
            $paymentHandler = new PlacetopayHandler();
            $paymentHandler->getRequestInfo(Arr::get($order->payment_data, 'requestId'));

            // update the order with payment data
            $this->orderRepository->update($order, [
                'payment_data->status' => Arr::get($paymentHandler->getContent(), 'status'),
            ]);
        }
    }
}
