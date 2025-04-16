<?php

declare(strict_types=1);

namespace D413\LaravelImport\Transfers;

interface ResponseDtoInterface
{
    public static function fromResponse(array $data): self;
}