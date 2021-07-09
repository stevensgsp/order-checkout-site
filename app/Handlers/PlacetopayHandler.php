<?php

namespace App\Handlers;

use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use GuzzleHttp\Psr7\Utils as GuzzleUtils;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PlacetopayHandler
{
    /**
     * @var array
     */
    protected $auth;

    /**
     * @var array|null
     */
    protected $payment;

    /**
     * @var array|null
     */
    protected $content;

    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct()
    {
        $nonce = Str::random(32);
        $seed = date('c');

        $this->auth = [
            'login'   => config('services.placetopay.login'),
            'tranKey' => base64_encode(sha1($nonce . $seed . config('services.placetopay.trankey'), true)),
            'nonce'   => base64_encode($nonce),
            'seed'    => $seed,
        ];
    }

    /**
     * Set payment.
     *
     * @see https://placetopay.github.io/web-checkout-api-docs/?shell#redirectrequest
     *
     * @param  array  $attributes
     * @return self
     */
    public function setPayment(array $attributes = []): self
    {
        $this->payment = $attributes;
        $this->payment['allowPartial'] = false;

        return $this;
    }

    /**
     * Execute preference.
     *
     * @return self
     *
     * @throws \Exception
     */
    public function request(): self
    {
        if (empty($this->payment)) {
            throw new Exception('You should set the payment.');
        }

        $response = $this->makeRequest('/api/session', $this->requestPayload());
        $this->content = $this->getRequestContents($response);

        return $this;
    }

    /**
     * Execute preference.
     *
     * @param  mixed  $requestId
     * @return self
     */
    public function getRequestInfo($requestId): self
    {
        $response = $this->makeRequest("/api/session/$requestId", ['auth' => $this->auth]);
        $this->content = $this->getRequestContents($response);

        return $this;
    }

    /**
     * Make the request.
     *
     * @param  array  $request
     * @return \GuzzleHttp\Psr7\GuzzleResponse
     */
    protected function makeRequest(string $uri, array $request): GuzzleResponse
    {
        $client = new GuzzleClient();

        try {
            $response = $client->request('POST', config('services.placetopay.url') . $uri, [
                'body' => GuzzleUtils::streamFor(json_encode($request)),
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ]
            ]);
        } catch (GuzzleRequestException $e) {
            $response =  $e->getResponse();
        }

        return $response;
    }

    /**
     * Returns the request's payload.
     *
     * @return array
     */
    protected function requestPayload(): array
    {
        return [
            'auth'       => $this->auth,
            'payment'    => $this->payment,
            'expiration' => date('c', strtotime('+1 days')),
            'returnUrl'  => route('orders.show', ['orderId' => Arr::get($this->payment, 'reference', -1)]),
            'ipAddress'  => request()->ip(),
            'userAgent'  => request()->server('HTTP_USER_AGENT'),
        ];
    }

    /**
     * Returns the request's content.
     *
     * @param  \GuzzleHttp\Psr7\GuzzleResponse  $response
     * @return array
     */
    protected function getRequestContents(GuzzleResponse $response): array
    {
        $stringContent = $response->getBody()->getContents();

        return (array) json_decode($stringContent, true);
    }

    /**
     * Get the response's status.
     *
     * @return string
     */
    public function getStatus(): string
    {
        return Arr::get($this->content, 'status.status');
    }

    /**
     * Determines if the response is successful.
     *
     * @return boolean
     */
    public function isSuccessful(): bool
    {
        $status = $this->getStatus();

        return (! empty($status) && ! in_array($status, ['FAILED']));
    }

    /**
     * Get the content of the response.
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get the request id of the response.
     *
     * @return mixed
     */
    public function getRequestId()
    {
        return Arr::get($this->content, 'requestId');
    }

    /**
     * Get the process url of the response.
     *
     * @return mixed
     */
    public function getProcessUrl()
    {
        return Arr::get($this->content, 'processUrl');
    }
}
