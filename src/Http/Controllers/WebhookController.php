<?php

namespace MartinBean\Laravel\Mux\Http\Controllers;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use MartinBean\Laravel\Mux\Events\WebhookReceived;
use MartinBean\Laravel\Mux\Http\Middleware\VerifyWebhookSignature;
use Symfony\Component\HttpFoundation\Response;

class WebhookController extends Controller
{
    /**
     * The event dispatcher implementation.
     */
    protected Dispatcher $events;

    /**
     * The configuration repository implementation.
     */
    protected Repository $config;

    /**
     * Create a new controller instance.
     */
    public function __construct(Dispatcher $events, Repository $config)
    {
        $this->events = $events;
        $this->config = $config;

        if ($this->config->get('mux.webhook.secret')) {
            $this->middleware(VerifyWebhookSignature::class);
        }
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        $payload = json_decode($request->getContent(), true);

        $this->events->dispatch(new WebhookReceived($payload));

        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
