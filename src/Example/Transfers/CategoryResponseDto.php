<?php

declare(strict_types=1);

namespace D413\LaravelImport\Example\Transfers;

use D413\LaravelImport\Transfers\ResponseDtoInterface;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

final class CategoryResponseDto extends Data implements ResponseDtoInterface
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $slug,
        public ?string $description,
        #[MapInputName('isActive')]
        public bool $is_active,
        #[MapInputName('parentId')]
        public ?int $parent_id,
        #[MapInputName('id')]
        public int $origin_id,
    ) {
    }

    public static function fromResponse(array $data): ResponseDtoInterface
    {
        $validatedData = self::validate($data);

        return new self(
            id: null,
            name: $validatedData['name'],
            slug: $validatedData['slug'],
            description: $validatedData['description'] ?? null,
            is_active: $validatedData['isActive'],
            parent_id: $validatedData['parentId'] ?? null,
            origin_id: $validatedData['id'],
        );
    }
}
