<?php

declare(strict_types=1);

namespace Riidme\Response;

final class ShortenResponse
{
    public function __construct(
        private readonly string $shortUrl
    ) {}

    public function getShortUrl(): string
    {
        return $this->shortUrl;
    }
}