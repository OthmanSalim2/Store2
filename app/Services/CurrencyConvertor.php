<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CurrencyConvertor
{
    private $apiKey;

    protected $baseUrl = "https://free.currconv.com/api/v7";

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function convert(string $from, string $to, float $amount = 1): float
    {
        $q = "{$from}_{$to}";

        $response = Http::baseUrl($this->baseUrl)
            // ->withHeaders([])
            // here possible instead of get be put or post ....
            ->get('/convert', [
                'q' => $q,
                'compact' => 'y',
                'apiKey' => $this->apiKey,
            ]);

        $result = $response->json();

        // here val within response mean within json returned .
        return $result[$q]['val'] * $amount;
    }
}
