<?php

namespace App\Http\Services;

use App\Models\Index;
use DateTime;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ScrapeMarketDataService
{

    private Client $client;

    public function __construct(
        private Index $index,
        private DateTime $startDate,
        private DateTime $endDate
    ) {
        $this->client = new Client();
    }

    /**
     * @throws \DateMalformedStringException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Random\RandomException
     * @throws \JsonException
     */
    public function scrape()
    {
        $baseURL = $this->index->dataSource()->base_url;
        $date         = $this->startDate;

        while ($date <= $this->endDate && $date <= now()) {
            $dateFormatted = $date->format('Y-m-d');
            $randomDelay   = random_int(1000, 3000);

            $url        = $baseURL.$date->format('Ymd');
            $response = $this->client->request('GET', $url, ['delay' => $randomDelay]);
            $statusCode = $response->getStatusCode();

            if ($statusCode === 200) {
                $indexDirectory = storage_path('holdingsData/'.$this->index->id);

                if ( ! is_dir($indexDirectory)) {
                    mkdir($indexDirectory);
                }

                $filename = storage_path('holdingsData/'.$this->index->id.'/'.$dateFormatted.'.json');
                $response = $response->getBody()->getContents();

                $response = str_replace("\u{FEFF}", '', $response);
                $response = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

                Log::info('Received a response');

                if ( ! empty($response['aaData'])) {
                    $jsonData = json_encode($response, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
                    file_put_contents($filename, $jsonData);
                } else {
                    Log::info('Data in response is empty');

                    $date = $date->modify('next day');
                    continue;
                }
            } else {
                Log::info('Request failed with status code: '.$statusCode);
            }

            $date = $date->modify('first day of next month');
        }
    }

}