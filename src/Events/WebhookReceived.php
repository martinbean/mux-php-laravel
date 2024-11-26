<?php

namespace MartinBean\Laravel\Mux\Events;

class WebhookReceived
{
    /**
     * The webhook payload.
     */
    public array $payload;

    /**
     * Create a new event instance.
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }
}
