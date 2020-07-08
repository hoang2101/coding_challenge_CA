<?php
namespace App\Controller;

use App\Entity\Review;
use App\Helper\QuarterCalculatorHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use FOS\RestBundle\Controller\Annotations as Rest;

class BenchmarkController extends AbstractController
{
    /**
     * @return JsonResponse
     * @Rest\Route("/benchmark/", methods={"GET"})
     */
    public function index(Request $request) : Response
    {
        $hotelId = $request->query->get('id');
        $startDate = \DateTime::createFromFormat('Y-m-d', $request->query->get('startDate'));
        $endDate = \DateTime::createFromFormat('Y-m-d', $request->query->get('endDate'));

        if (empty($hotelId) || empty($startDate) || empty($endDate)) {
            return new JsonResponse("Wrong parameters",
                Response::HTTP_NOT_FOUND);
        }

        if ($startDate > $endDate) {
            return new JsonResponse("End date should not before start date",
                Response::HTTP_NOT_FOUND);
        }

        $connection = $this->getDoctrine()->getManager();
        $overTimeScoreList = $connection->getRepository(Review::class)->getAverageScore(
            $startDate,
            $endDate
        );

        $hotelCounter = 0;
        $total = 0;
        $quarterArray = [];
        foreach($overTimeScoreList as $overTimeScore) {
            if ($overTimeScore['hotel_id'] == $hotelId) {
                $benchmarkResult['hotelAverage'] = $overTimeScore['average_score'];
            } else {
                $hotelCounter ++;
                $total += $overTimeScore['average_score'];
            }
            $quarterArray[] = $overTimeScore['average_score'];
        }
        $benchmarkResult['allOtherHotelAverage'] = \round($total / $hotelCounter, 1);

        $helper = new QuarterCalculatorHelper();
        $benchmarkResult['rankType'] = $helper->getRankFromArray($quarterArray, $benchmarkResult['hotelAverage']);

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $jsonContent = $serializer->serialize($benchmarkResult, 'json');
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}