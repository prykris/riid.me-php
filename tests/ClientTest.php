<?php

declare(strict_types=1);

namespace Riidme\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Riidme\Client;
use Riidme\Exception\RiidmeException;

/**
 * @covers \Riidme\Client
 */
class ClientTest extends TestCase
{
    /**
     * @covers \Riidme\Client::shorten
     */
    public function testShortenUrlSuccess(): void
    {
        // Mock the response stream
        $mockStream = $this->createMock(StreamInterface::class);
        $mockStream->expects($this->once())
            ->method('getContents')
            ->willReturn(json_encode(['short_url' => 'https://riid.me/abc123']));

        // Mock the response
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);
        $mockResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($mockStream);

        // Mock the request
        $mockRequest = $this->createMock(RequestInterface::class);
        $mockRequest->expects($this->once())
            ->method('withHeader')
            ->with('Content-Type', 'application/json')
            ->willReturnSelf();
        $mockRequest->expects($this->once())
            ->method('getBody')
            ->willReturn($mockStream);

        // Mock the request factory
        $mockFactory = $this->createMock(RequestFactoryInterface::class);
        $mockFactory->expects($this->once())
            ->method('createRequest')
            ->with('POST', 'https://riid.me/shorten')
            ->willReturn($mockRequest);

        // Mock the HTTP client
        $mockClient = $this->createMock(ClientInterface::class);
        $mockClient->expects($this->once())
            ->method('sendRequest')
            ->with($mockRequest)
            ->willReturn($mockResponse);

        // Create client and test
        $client = new Client(
            httpClient: $mockClient,
            requestFactory: $mockFactory,
            baseUrl: 'https://riid.me'
        );
        
        $response = $client->shorten('https://example.com');
        $this->assertSame('https://riid.me/abc123', $response->getShortUrl());
        $this->assertSame('https://riid.me/abc123', (string) $response);
    }

    /**
     * @covers \Riidme\Client::shorten
     */
    public function testInvalidUrl(): void
    {
        $mockClient = $this->createMock(ClientInterface::class);
        $mockFactory = $this->createMock(RequestFactoryInterface::class);
        
        $client = new Client($mockClient, $mockFactory);
        
        $this->expectException(RiidmeException::class);
        $this->expectExceptionMessage('Invalid URL provided');
        
        $client->shorten('not-a-url');
    }
} 