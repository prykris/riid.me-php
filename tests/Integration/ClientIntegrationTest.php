<?php

declare(strict_types=1);

namespace Riidme\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Riidme\Client;
use Riidme\Exception\RiidmeException;

/**
 * @group integration
 */
class ClientIntegrationTest extends TestCase
{
    private Client $client;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Skip integration tests if no API access
        if (!getenv('RIIDME_API_URL')) {
            $this->markTestSkipped('Integration tests require RIIDME_API_URL environment variable');
        }
        
        $this->client = new Client([
            'base_url' => getenv('RIIDME_API_URL'),
            'timeout' => 10,
            'retries' => 2
        ]);
    }
    
    public function testCanShortenRealUrl(): void
    {
        $longUrl = 'https://www.example.com/some/very/long/url/that/needs/shortening?' . uniqid();
        
        $result = $this->client->shorten($longUrl);
        
        $this->assertStringStartsWith('https://riid.me/', $result->getShortUrl());
        
        // Verify the shortened URL returns a redirect
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $result->getShortUrl());
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->assertContains($statusCode, [301, 302], 'Short URL should return a redirect status code');
        
        curl_close($ch);
    }
    
    public function testHandlesApiErrors(): void
    {
        $this->expectException(RiidmeException::class);
        
        // Try to shorten an invalid URL
        $this->client->shorten('not-a-valid-url');
    }
    
    /**
     * @dataProvider validUrlProvider
     */
    public function testHandlesVariousUrlFormats(string $url): void
    {
        $result = $this->client->shorten($url);
        $this->assertNotEmpty($result->getShortUrl());
    }
    
    public static function validUrlProvider(): array
    {
        return [
            'simple url' => ['https://example.com'],
            'url with query' => ['https://example.com?foo=bar&baz=qux'],
            'url with fragment' => ['https://example.com#section'],
            'url with path' => ['https://example.com/path/to/resource'],
            'url with port' => ['https://example.com:8080/path'],
            'url with everything' => ['https://example.com:8080/path?foo=bar#section'],
        ];
    }
} 