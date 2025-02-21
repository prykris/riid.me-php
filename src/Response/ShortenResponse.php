<?php

declare(strict_types=1);

namespace Riidme\Response;

final readonly class ShortenResponse
{
    public function __construct(
        private string $shortUrl
    ) {
    }

    public function getShortUrl(): string
    {
        return $this->shortUrl;
    }

    public function __toString(): string
    {
        return $this->shortUrl;
    }
}