<?php

declare(strict_types=1);

namespace D413\LaravelImport\Commands;

use D413\LaravelImport\Connector\EntityImportConnectorInterface;
use D413\LaravelImport\Exceptions\ImportConfigurationException;
use D413\LaravelImport\Exceptions\ImportTransportException;
use D413\LaravelImport\Transfers\ImportRequestDto;
use D413\LaravelImport\Transfers\ResponseDtoInterface;
use Generator;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;
use Symfony\Component\Console\Question\Question;

/** @template T of object */
final class ImportCategoriesCommand extends Command
{
    protected $signature = 'import';

    protected $description = 'Universal importer for various entities from old website';

    public function __construct(
        protected EntityImportConnectorInterface $connector,
    ) {
        parent::__construct();
    }

    /**
     * @throws ImportConfigurationException
     * @throws ImportTransportException
     */
    public function handle(): void
    {
        $data = $this->getPreparedData();

        /** @var Generator<Collection<int, T>> $collection */
        $collection = $this->connector->stream(importRequestDto: $data['importRequestDto']);

        /** @var Collection<int, T> $pageCollection */
        foreach ($collection as $pageCollection) {
            $chunkSize = max(1, (int)floor($data['limit'] / 10));

            $pageCollection->chunk(size: $chunkSize)->each(function (Collection $chunk) use ($data) {
                event(new $data['event']($chunk));
            });
        }

        $this->components->info('Added to the queue');
    }

    /**
     * @return array{
     *      responseDto: class-string<T>,
     *      event: string,
     *      limit: int,
     *      importRequestDto: ImportRequestDto
     *  }
     * @throws ImportConfigurationException
     */
    private function getPreparedData(): array
    {
        /** @var class-string<T> $responseDto */
        $responseDto = $this->getEntity();
        $limit = $this->getLimit();
        $page = $this->getPage();
        $endpoint = $this->getEndpoint($responseDto);
        $event = $this->getImportProcessingEvent($responseDto);

        $parts = explode("\\", $responseDto);
        $entity = end($parts);
        $this->components->info("Importing {$entity} with a limit of {$limit} and from page {$page}");

        $this->startLog();

        $importRequestDto = new ImportRequestDto(
            endpoint: $endpoint,
            limit: $limit,
            page: $page,
            resultDto: $responseDto
        );

        return [
            'responseDto' => $responseDto,
            'event' => $event,
            'limit' => $limit,
            'importRequestDto' => $importRequestDto,
        ];
    }

    /** @return class-string<T>
     * @throws ImportConfigurationException
     */
    private function getEntity(): string
    {
        /** @var array<array{dto: class-string<T>, endpoint: string, event: class-string}> $entities */
        $entities = array_column(config('import.import_entities'), 'dto');

        $selectedDto = $this->choice(
            question: 'What do you want to import ? ',
            choices: $entities,
            attempts: 3,
            multiple: false
        );

        if (!in_array(ResponseDtoInterface::class, class_implements($selectedDto), true)) {
            throw new ImportConfigurationException("{$selectedDto} must implement ResponseDtoInterface.");
        }

        return $selectedDto;
    }

    private function getLimit(): int
    {
        return $this->askWithValidation(
            questionText: 'Specify the limit(from 1 and above): ',
            default: 1000,
            validator: function ($input) {
                if (!is_numeric($input) || (int)$input < 1) {
                    throw new InvalidArgumentException(message: 'Limit must be a number from 1 and above . ');
                }
                return (int)$input;
            }
        );
    }

    private function getPage(): int
    {
        return $this->askWithValidation(
            questionText: 'Specify the page number (from 1 and above):',
            default: 1,
            validator: function ($input) {
                if (!is_numeric($input) || (int)$input < 1) {
                    throw new InvalidArgumentException(message: 'Page number must be a number from 1 and above.');
                }
                return (int)$input;
            }
        );
    }

    /**
     * @param string $questionText
     * @param int $default
     * @param callable $validator
     * @return int
     */
    private function askWithValidation(string $questionText, int $default, callable $validator): int
    {
        $question = new Question(question: $questionText, default: $default);
        $question->setValidator($validator);

        $maxAttempts = 3;
        $attempts = 0;

        while ($attempts < $maxAttempts) {
            try {
                return (int) $this->output->askQuestion($question);
            } catch (InvalidArgumentException $e) {
                $this->components->error($e->getMessage());
                $attempts++;
            }
        }

        $this->components->error(string: "Exceeded number of attempts ($maxAttempts).");

        exit(1);
    }

    /**
     * @throws ImportConfigurationException
     */
    private function getEndpoint(string $responseDto): string
    {
        /** @var array<array{dto: class-string, endpoint: string, event: class-string}> $entities */
        $entitiesConfig = config('import.import_entities', []);

        foreach ($entitiesConfig as $entity) {
            if ($entity['dto'] === $responseDto) {
                return $entity['endpoint'];
            }
        }

        throw new ImportConfigurationException(message: "Endpoint not found for {$responseDto}.");
    }

    /**
     * @throws ImportConfigurationException
     */
    private function getImportProcessingEvent(string $responseDto): string
    {
        /** @var array<array{dto: class-string, endpoint: string, event: class-string}> $entities */
        $entitiesConfig = config('import.import_entities', []);

        foreach ($entitiesConfig as $entity) {
            if ($entity['dto'] === $responseDto) {
                return $entity['event'];
            }
        }

        throw new ImportConfigurationException(message: "Queue class not found: {$responseDto}.");
    }

    private function startLog(): void
    {
        $logPath = storage_path('logs/imports.log');

        if (!File::exists($logPath)) {
            File::ensureDirectoryExists(dirname($logPath));
            File::put($logPath, '');
        }

        $currentDateTime = now()->format('Y-m-d H:i:s');
        File::append($logPath, "New records: {$currentDateTime}\n");
    }
}
