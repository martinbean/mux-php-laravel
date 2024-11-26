# Mux PHP SDK for Laravel

## Installation
```sh
composer require martinbean/mux-php-laravel
```

## Configuration
By default, the package expects two environment variables to be defined:

- `MUX_CLIENT_ID`
- `MUX_CLIENT_SECRET`

This should be a Mux access token ID and secret.

You can generate a Mux access token by:

1. Logging into your [Mux dashboard][1]
2. Going to “Settings”
3. Then going to “Access Tokens”

### Verifying webhook signatures
To verify webhook signatures from Mux, specify the signing secret for your webhook endpoint as the value of your `MUX_WEBHOOK_SECRET` environment variable. This means you will need to register your webhook endpoint in the [Mux dashboard][1] first.

## Usage

### API clients
The package will register default configuration based on the configured Mux access token. This means you can then type-hint a Mux client in your Laravel application, and it and its dependencies will be automatically resolved:

```php
namespace App\Http\Controllers;

use MuxPhp\Api\DirectUploadsApi;

class UploadController extends Controller
{
    protected DirectUploadsApi $uploads;

    public function __construct(DirectUploadsApi $uploads)
    {
        $this->uploads = $uploads;
    }
}
```

### Webhook handling
The package will also automatically register a route to listen for webhooks sent by Mux. The URI of this handler is **/mux/webhook**

The webhook handler is heavily based on [Cashier][2]’s webhook handler. When a webhook from Mux is received, an instance of the `MartinBean\Laravel\Mux\Events\WebhookReceived` event is dispatched containing the webhook’s payload.

You can define a listener that listens for this event:

```php
namespace App\Listeners;

use MartinBean\Laravel\Mux\Events\WebhookReceived;

class MuxEventListener
{
    public function handle(WebhookReceived $event): void
    {
        // Handle Mux webhook event...
    }
}
```

This is where you would update models, etc in your application based on events from Mux:

```diff
  public function handle(WebhookReceived $event): void
  {
-     // Handle Mux webhook event...
+     match ($event->payload['type']) {
+         'video.asset.errored' => $this->handleVideoAssetErrored($event->payload),
+         'video.asset.ready' => $this->handleVideoAssetReady($event->payload),
+         default => null, // unhandled event
+     };
  }
+
+ protected function handleVideoAssetErrored(array $payload): void
+ {
+     // TODO: Retrieve associated video
+     // TODO: Update video status to 'errored'
+     // TODO: Notify user processing video errored
+ }
+
+ protected function handleVideoAssetReady(array $payload): void
+ {
+     // TODO: Retrieve associated video
+     // TODO: Update video status to 'ready'
+     // TODO: Notify user processing video succeeded
+ }
```

## Contribution
Contributions are always welcome. Please open a pull request with your proposed changes, with accompanying tests.

## License
Licensed under the [MIT License](LICENSE.md)

[1]: https://dashboard.mux.com/
[2]: https://laravel.com/docs/cashier
