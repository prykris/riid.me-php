<?php

declare(strict_types=1);

namespace Riidme;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Log\LoggerInterface;
use Riidme\Exception\RiidmeException;
use Riidme\Http\ClientFactory;
use Riidme\Response\ShortenResponse;

final readonly class Client
{
    private const DEFAULT_TIMEOUT = 5;
    private const DEFAULT_RETRIES = 3;
    private const DEFAULT_BASE_URL = 'https://riid.me';

    public function __construct(
        private ClientInterface $httpClient,
        private RequestFactoryInterface $requestFactory,
        private ?LoggerInterface $logger = null,
        private string $baseUrl = self::DEFAULT_BASE_URL,
        private int $timeout = self::DEFAULT_TIMEOUT,
        private int $retries = self::DEFAULT_RETRIES,
    ) {
    }

    public static function create(array $config = []): self
    {
        $factory = new ClientFactory();

        return new self(
            httpClient: $factory->createClient($config['timeout'] ?? self::DEFAULT_TIMEOUT),
            requestFactory: $factory->createRequestFactory(),
            baseUrl: $config['base_url'] ?? self::DEFAULT_BASE_URL,
            timeout: $config['timeout'] ?? self::DEFAULT_TIMEOUT,
            retries: $config['retries'] ?? self::DEFAULT_RETRIES,
        );
    }

    public function shorten(string $longUrl): ShortenResponse
    {
        if (!filter_var($longUrl, FILTER_VALIDATE_URL)) {
            throw new RiidmeException('Invalid URL provided');
        }

        $request = $this->requestFactory->createRequest('POST', "{$this->baseUrl}/shorten")
            ->withHeader('Content-Type', 'application/json');

        $request->getBody()->write(json_encode(['long_url' => $longUrl], JSON_THROW_ON_ERROR));

        try {
            $response = $this->httpClient->sendRequest($request);

            return match ($response->getStatusCode()) {
                200 => new ShortenResponse(
                    json_decode($response->getBody()->getContents(), true, flags: JSON_THROW_ON_ERROR)['short_url']
                ),
                default => throw new RiidmeException(
                    "API request failed: {$response->getBody()->getContents()}",
                    $response->getStatusCode()
                )
            };
        } catch (\JsonException $e) {
            $this->logger?->error('Invalid JSON response', [
                'url' => $longUrl,
                'error' => $e->getMessage()
            ]);
            throw new RiidmeException('Invalid response format', previous: $e);
        } catch (\Throwable $e) {
            $this->logger?->error('Failed to shorten URL', [
                'url' => $longUrl,
                'error' => $e->getMessage()
            ]);
            throw new RiidmeException('Failed to shorten URL: ' . $e->getMessage(), previous: $e);
        }
    }
}