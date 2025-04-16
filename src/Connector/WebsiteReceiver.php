<?php

namespace D413\LaravelImport\Connector;

use D413\LaravelImport\Exceptions\ImportResponseException;
use D413\LaravelImport\Transfers\ImportRequestDto;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Throwable;

final class WebsiteReceiver implements WebsiteReceiverInterface
{
    public const int TIMEOUT = 120;

    private const int MAX_RETRIES = 3;

    /**
     * @throws ImportResponseException
     */
    public function receive(ImportRequestDto $requestDto): Collection
    {
        $url = config('import.website_config.url') . $requestDto->endpoint;
        $key = config('import.website_config.key');
        $attempt = 0;

        while ($attempt < self::MAX_RETRIES) {
            try {
                $response = Http::asForm()
                    ->timeout(seconds: self::TIMEOUT)
                    ->withHeaders([
                        'Accept' => 'application/json',
                        'Authorization' => $key,
                        'Content-Type' => 'multipart/form-data',
                    ])
                    ->post($url, $requestDto->toArray());

                if (!$response->ok()) {
                    throw new ImportResponseException(
                        message: 'The external resource responded with an error. Status: ' . $response->status(),
                        code: $response->status(),
                        url: $url,
                        requestPayload: $requestDto->toArray(),
                        response: $response
                    );
                }

                $data = $response->json('data') ?? $response->json();

                if (!is_array($data)) {
                    throw new ImportResponseException(
                        message: 'Invalid response format from the external resource.',
                        url: $url,
                        requestPayload: $requestDto->toArray(),
                        response: $response
                    );
                }

                return collect($data)->map(fn(array $item) => $requestDto->resultDto::fromResponse($item));
            } catch (Throwable $e) {
                $attempt++;

                if ($attempt >= self::MAX_RETRIES) {
                    throw new ImportResponseException(
                        message: 'Failed to get a valid response from the external resource.',
                        previous: $e,
                        url: $url,
                        requestPayload: $requestDto->toArray()
                    );
                }
                sleep(1);
            }
        }

        throw new ImportResponseException(
            message: 'Failed to get a response from the external resource after multiple attempts.',
            url: $url,
            requestPayload: $requestDto->toArray()
        );
    }
}