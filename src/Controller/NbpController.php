<?php

namespace App\Controller;

use App\Service\NbpApiService;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class NbpController extends AbstractController
{
   private NbpApiService $nbpApiService;
   public function __construct(NbpApiService $nbpApiService){
       $this->nbpApiService = $nbpApiService;

   }
    /**
     * @Route("/api/req", name="req", methods={"POST"})
     * @throws GuzzleException
     */
    public function req(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if (!($requestData['currencyCode'] ?? false && $requestData['startDate'] ?? false && $requestData['startDate'] ?? false)) {
            return new JsonResponse(["error" => "[currencyCode, startDate, endDate] must be specified"]);}

        $currencyCode = $requestData['currencyCode']; ;

        try {
            $startDate = new \DateTime($requestData['startDate']);
        } catch (\Exception $e) {
            return new JsonResponse(['error'=>$e->getMessage()]);
        }

        try {
            $endDate = new \DateTime($requestData['endDate']);
        } catch (\Exception $e) {
            return new JsonResponse(['error'=>$e->getMessage()]);
        }

        if(!$this->isDateCorrect($startDate,$endDate)[0]){
            return new JsonResponse(['error'=>$this->isDateCorrect($startDate,$endDate)[1]]);
        };

        $rates = $this->nbpApiService->getRatesForCurrencyAndDateRange($currencyCode, $startDate, $endDate);

        if(isset($rates["error"])){
            return new JsonResponse(["error" =>$rates["error"] ]);
        }

        $responseData = count($rates->Rates->Rate) > 1 ? $rates->Rates : ["Rate" => [$rates->Rates->Rate]];

        $lastCurrencyValue = null;
        if(isset($responseData->Rate))
        for($i = 0 ; $i< count($responseData->Rate); $i++ ){
            if( $i === 0 ){
                $lastCurrencyValue = [floatval($responseData->Rate[$i]->Bid), floatval($responseData->Rate[$i]->Ask)];
                continue;
            }
            $diff = array(
                floatval(number_format(floatval($responseData->Rate[$i]->Bid) - $lastCurrencyValue[0], 2)),
                floatval(number_format(floatval($responseData->Rate[$i]->Ask) - $lastCurrencyValue[1], 2))
            );
            $lastCurrencyValue = [floatval($responseData->Rate[$i]->Bid), floatval($responseData->Rate[$i]->Ask)];

            $responseData->Rate[$i]->Diff = json_encode($diff);
        }

        return new JsonResponse($responseData);
    }

    /**
     * @Route("/", name="view")
     */
    public function view(){
        return $this->render('index.html');
    }

    private function isDateCorrect($startDate, $endDate){
        if($endDate < $startDate) return [false, "End Date cannot be earlier than Start Date"];
        if(($startDate->diff($endDate))->days > 7 ) return [false, "Maximum 7 days difference"];
        if($startDate >= new \DateTime() || $endDate >= new \DateTime()) return [false, "Date cannot be set in future"];
        return [true];}
}