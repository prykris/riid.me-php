<?php

declare(strict_types=1);

namespace Riidme\Http;

use Http\Discovery\Psr17FactoryDiscovery;
use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

final class ClientFactory
{
    public function createClient(int $timeout): ClientInterface
    {
        $client = Psr18ClientDiscovery::find();
        
        // Configure timeout if client supports it
        if (method_exists($client, 'setTimeout')) {
            $client->setTimeout($timeout);
        }

        return $client;
    }

    /**
     * Create a request factory instance
     *
     * @return RequestFactoryInterface
     */
    public function createRequestFactory(): RequestFactoryInterface
    {
        return Psr17FactoryDiscovery::findRequestFactory();
    }
}