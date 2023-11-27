<?php

namespace Support\ValueObjects;

use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;

class URL
{
    /**
     * @throws \Exception
     */
    public function __construct(
        protected readonly string $baseUrl,
        protected readonly array $queryParams = []
    ) {
        if (! Str::isUrl($this->fullUrl())) {
            throw new \Exception("invalid url: {$this->fullUrl()}");
        }

    }

    public static function make(string $baseUrl, array $queryParams = []): self
    {
        return new self($baseUrl, $queryParams);
    }

    public function baseUrl(): string
    {
        return $this->baseUrl;
    }

    public function queryParams(): array
    {
        return $this->queryParams;
    }

    public function fullUrl(): string
    {
        $queryString = http_build_query($this->queryParams);

        return $this->baseUrl.($queryString ? '?'.$queryString : '');
    }

    #[NoReturn]
    public function dd(): void
    {
        dd(
            $this->baseUrl,
            $this->queryParams,
        );
    }

    public function __toString(): string
    {
        return $this->fullUrl();
    }
}
