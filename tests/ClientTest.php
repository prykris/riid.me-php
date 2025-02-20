<?php

declare(strict_types=1);

namespace Riidme\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
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
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockStream = $this->createMock(StreamInterface::class);
        
        $mockStream->expects($this->once())
            ->method('getContents')
            ->willReturn(json_encode(['short_url' => 'https://riid.me/abc123']));
            
        $mockResponse->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(200);
            
        $mockResponse->expects($this->once())
            ->method('getBody')
            ->willReturn($mockStream);

        $mockClient = $this->createMock(ClientInterface::class);
        $mockClient->expects($this->once())
            ->method('sendRequest')
            ->willReturn($mockResponse);

        $client = new Client([], $mockClient);
        $response = $client->shorten('https://example.com');

        $this->assertEquals('https://riid.me/abc123', $response->getShortUrl());
    }

    /**
     * @covers \Riidme\Client::shorten
     */
    public function testInvalidUrl(): void
    {
        $client = new Client();
        
        $this->expectException(RiidmeException::class);
        $this->expectExceptionMessage('Invalid URL provided');
        
        $client->shorten('not-a-url');
    }
} 