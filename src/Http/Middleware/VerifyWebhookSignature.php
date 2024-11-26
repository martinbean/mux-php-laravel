<?php

namespace MartinBean\Laravel\Mux\Http\Middleware;

use Closure;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use MartinBean\Laravel\Mux\Exceptions\SignatureException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class VerifyWebhookSignature
{
    /**
     * The configuration repository implementation.
     */
    protected Repository $config;

    /**
     * Create a new middleware instance.
     */
    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     *
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $this->verifySignature($request);
        } catch (SignatureException $e) {
            throw new AccessDeniedHttpException($e->getMessage(), $e);
        }

        return $next($request);
    }

    /**
     * Verify the Mux webhook signature.
     *
     * @throws \MartinBean\Laravel\Mux\Exceptions\SignatureException
     */
    protected function verifySignature(Request $request): void
    {
        $timestamp = $this->extractTimestamp($request->header('Mux-Signature'));
        $signature = $this->extractSignature($request->header('Mux-Signature'));

        $payload = sprintf('%s.%s', $timestamp, $request->getContent());
        $expectedSignature = $this->computeSignature($payload, $this->config->get('mux.webhook.secret'));

        if (! hash_equals($expectedSignature, $signature)) {
            throw new SignatureException('Signature does not match the expected signature for the payload');
        }

        // TODO: Check if timestamp is within tolerance
    }

    /**
     * Extract the timestamp in the signature header.
     *
     * @throws \MartinBean\Laravel\Mux\Exceptions\SignatureException
     */
    protected function extractTimestamp(string $header): int
    {
        $items = explode(',', $header);

        foreach ($items as $item) {
            [$key, $value] = explode('=', $item, 2);

            if ($key === 't') {
                if (is_numeric($value)) {
                    return (int) $value;
                }
            }
        }

        throw new SignatureException('Unable to extract timestamp from header');
    }

    /**
     * Extract the signature in the signature header.
     *
     * @throws \MartinBean\Laravel\Mux\Exceptions\SignatureException
     */
    protected function extractSignature(string $header): string
    {
        $items = explode(',', $header);

        foreach ($items as $item) {
            [$key, $value] = explode('=', $item, 2);

            if ($key === 'v1') {
                return $value;
            }
        }

        throw new SignatureException('Unable to extract signature from header');
    }

    /**
     * Compute the signature for the given payload and secret.
     */
    protected function computeSignature(string $payload, string $secret): string
    {
        return hash_hmac('sha256', $payload, $secret);
    }
}
