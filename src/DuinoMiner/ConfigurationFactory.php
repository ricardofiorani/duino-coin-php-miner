<?php declare(strict_types=1);

namespace RicardoFiorani\DuinoMiner;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Client\ClientExceptionInterface;
use RicardoFiorani\DuinoMiner\Connectivity\Pool;
use RicardoFiorani\GuzzlePsr18Adapter\Client as HttpClient;

class ConfigurationFactory
{
    private string $configEndpoint;

    public function __construct(string $configEndpoint)
    {
        $this->configEndpoint = $configEndpoint;
    }

    public function createConfiguration(string $username): Configuration
    {
        $pool = $this->getPool();

        return new Configuration($pool, $username);
    }

    /**
     * @throws ClientExceptionInterface
     */
    private function getPool(): Pool
    {
        $client = new HttpClient();
        $request = new Request('GET', $this->configEndpoint);
        $response = $client->sendRequest($request);
        $parsedResponse = json_decode((string)$response->getBody(), true);

        return new Pool(
            $parsedResponse['name'],
            $parsedResponse['ip'],
            $parsedResponse['port'],
        );
    }
}
