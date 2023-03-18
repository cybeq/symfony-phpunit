<?php

namespace tests;

use App\Service\NbpApiService;
use GuzzleHttp\Exception\GuzzleException;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NbpTest extends WebTestCase
{

    private NbpApiService $nbpApiService;

    protected function setUp(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $this->nbpApiService = $container->get(NbpApiService::class);
    }


    /**
     * @throws GuzzleException
     */
    public function testGetRatesForCurrencyAndDateRange(): void
    {
        $currencyCode = 'USD';
        $startDate = new \DateTime('2022-01-01');
        $endDate = new \DateTime('2022-01-05');

        $result = $this->nbpApiService->getRatesForCurrencyAndDateRange($currencyCode, $startDate, $endDate);

        $this->assertInstanceOf(SimpleXMLElement::class, $result);

    }
}
