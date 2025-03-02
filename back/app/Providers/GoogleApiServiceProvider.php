<?php

namespace App\Providers;

use Google\Client;
use Google\Service\Sheets;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Illuminate\Support\ServiceProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Message\RequestInterface;

class GoogleApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Client::class, function () {

            $log = new Logger('google_api');
            $log->pushHandler(
                new StreamHandler(
                    storage_path('logs/google_api.log'),
                    Logger::INFO
                )
            );

            $stack = HandlerStack::create();
            $stack->push(
                Middleware::tap(
                    function (RequestInterface $request) use ($log) {
                        $logInfo = [
                            'method' => $request->getMethod(),
                            'url' => (string)$request->getUri(),
                            'headers' => $request->getHeaders(),
                            'bodySize' => (string)$request->getBody()->getSize(),
                        ];
                        if (config('services.google.logFullBody')) {
                            $logInfo['body'] = (string)$request->getBody();
                        }
                        $log->info('Google API Request', $logInfo);
                    }
                )
            );

            $client = new Client();
            $client->setHttpClient(new \GuzzleHttp\Client(['handler' => $stack]));
            $client->addScope(Sheets::SPREADSHEETS);
            $client->setAuthConfig(
                storage_path(config('services.google.credentials'))
            );

            return $client;
        });

        $this->app->singleton(Sheets::class, function ($app) {
            return new Sheets($app->make(Client::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
