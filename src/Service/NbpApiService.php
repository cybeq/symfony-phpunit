<?php

namespace App\Service;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use SimpleXMLElement;
use Symfony\Component\HttpKernel\Log\Logger;

class NbpApiService
{
    private ClientInterface $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @throws GuzzleException
     * @throws \Exception
     */
    public function getRatesForCurrencyAndDateRange(string $currencyCode, \DateTime $startDate, \DateTime $endDate)
{
    $url = sprintf('http://api.nbp.pl/api/exchangerates/rates/c/%s/%s/%s/', $currencyCode, $startDate->format('Y-m-d'), $endDate->format('Y-m-d'));

    try {
        $response = $this->client->request('GET', $url, [
            'headers' => [
                'Accept' => 'application/xml'
            ]
        ]);
    } catch (GuzzleException $e) {
        return ["error"=>"There is no results to that specific dates"];}

    $xml = new SimpleXMLElement((string) $response->getBody());

    foreach ($xml->Rates->Rate as $rate) {
        $rate->Bid = number_format(floatval($rate->Bid), 2);
        $rate->Ask = number_format(floatval($rate->Ask), 2);
    }

    return $xml;
}
}
