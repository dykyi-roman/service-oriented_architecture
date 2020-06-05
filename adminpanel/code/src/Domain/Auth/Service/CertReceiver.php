<?php

declare(strict_types=1);

namespace App\Domain\Auth\Service;

use App\Domain\Auth\Exception\AuthException;
use App\Infrastructure\HttpClient\ResponseDataExtractorInterface;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Throwable;

final class CertReceiver
{
    private const CERT_URI = '/api/cert';

    private string $host;
    private ClientInterface $client;
    private ResponseDataExtractorInterface $responseDataExtractor;

    public function __construct(
        ClientInterface $client,
        ParameterBagInterface $bag,
        ResponseDataExtractorInterface $responseDataExtractor
    ) {
        $this->host = $bag->get('AUTH_SERVICE_HOST');
        $this->client = $client;
        $this->responseDataExtractor = $responseDataExtractor;
    }

    public function downloadPublicKey(string $path): bool
    {
        try {
            $request = new Request('GET', $this->host . self::CERT_URI);
            $response = $this->client->sendRequest($request);
            $key = $this->responseDataExtractor->extract($response);

            return (bool)file_put_contents($path, $key['data']['key']);
        } catch (Throwable $exception) {
            throw AuthException::publicKeyIsNotUpdated($exception->getMessage());
        }
    }
}
