<?php

namespace MartinBean\Laravel\Mux\Tests;

use Illuminate\Support\Facades\Event;
use MartinBean\Laravel\Mux\Events\WebhookReceived;
use MartinBean\Laravel\Mux\Http\Middleware\VerifyWebhookSignature;

class WebhookTest extends TestCase
{
    public function test_it_dispatches_webhook_received_event(): void
    {
        Event::fake([
            WebhookReceived::class,
        ]);

        $this
            ->withoutMiddleware(VerifyWebhookSignature::class)
            ->postJson('/mux/webhook', [
                'type' => 'test',
            ])
            ->assertSuccessful();

        Event::assertDispatched(function (WebhookReceived $event): bool {
            $this->assertEqualsCanonicalizing(
                expected: [
                    'type' => 'test',
                ],
                actual: $event->payload,
            );

            return true;
        });
    }
}
