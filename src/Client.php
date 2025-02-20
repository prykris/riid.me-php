<?php

declare(strict_types=1);

namespace Riidme;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Log\LoggerInterface;
use Riidme\Exception\RiidmeException;
use Riidme\Http\ClientFactory;
use Riidme\Response\ShortenResponse;

final class Client
{
    private const DEFAULT_TIMEOUT = 5;
    private const DEFAULT_RETRIES = 3;
    private const DEFAULT_BASE_URL = 'https://riid.me';

    private ClientInterface $httpClient;
    private RequestFactoryInterface $requestFactory;
    private ?LoggerInterface $logger;
    private string $baseUrl;
    private int $timeout;
    private int $retries;

    public function __construct(
        array $config = [],
        ?ClientInterface $httpClient = null,
        ?RequestFactoryInterface $requestFactory = null,
        ?LoggerInterface $logger = null
    ) {
        $this->baseUrl = $config['base_url'] ?? self::DEFAULT_BASE_URL;
        $this->timeout = $config['timeout'] ?? self::DEFAULT_TIMEOUT;
        $this->retries = $config['retries'] ?? self::DEFAULT_RETRIES;
        
        $factory = new ClientFactory();
        $this->httpClient = $httpClient ?? $factory->createClient($this->timeout);
        $this->requestFactory = $requestFactory ?? $factory->createRequestFactory();
        $this->logger = $logger;
    }

    public function shorten(string $longUrl): ShortenResponse
    {
        if (!filter_var($longUrl, FILTER_VALIDATE_URL)) {
            throw new RiidmeException('Invalid URL provided');
        }

        $request = $this->requestFactory->createRequest('POST', $this->baseUrl . '/shorten')
            ->withHeader('Content-Type', 'application/json');

        $request->getBody()->write(json_encode(['long_url' => $longUrl]));

        try {
            $response = $this->httpClient->sendRequest($request);
            
            if ($response->getStatusCode() !== 200) {
                throw new RiidmeException(
                    'API request failed: ' . $response->getBody()->getContents(),
                    $response->getStatusCode()
                );
            }

            $data = json_decode($response->getBody()->getContents(), true);
            
            return new ShortenResponse($data['short_url']);
        } catch (\Throwable $e) {
            $this->logger?->error('Failed to shorten URL', [
                'url' => $longUrl,
                'error' => $e->getMessage()
            ]);
            
            throw new RiidmeException('Failed to shorten URL: ' . $e->getMessage(), 0, $e);
        }
    }
} 