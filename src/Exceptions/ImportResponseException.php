<?php

namespace D413\LaravelImport\Exceptions;

use Illuminate\Http\Client\Response;
use Throwable;

class ImportResponseException extends ImportException
{
    public function __construct(
        string $message,
        int $code = 0,
        ?Throwable $previous = null,
        protected ?string $url = null,
        protected array $requestPayload = [],
        protected ?Response $response = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getRequestPayload(): array
    {
        return $this->requestPayload;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }
}