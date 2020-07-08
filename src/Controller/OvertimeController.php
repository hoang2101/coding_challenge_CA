<?php
namespace App\Controller;

use App\Entity\Review;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use FOS\RestBundle\Controller\Annotations as Rest;

class OvertimeController extends AbstractController
{
    /**
     * @return JsonResponse
     * @Rest\Route("/overtime/", methods={"GET"})
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
        $overTimeScoreList = $connection->getRepository(Review::class)->getAverageScoreByHotel(
            $hotelId,
            $startDate,
            $endDate
        );

        $dateDiff = $endDate->diff($startDate);
        $refinedOverTimeScoreList = array_map(function ($overTimeScore) use ($dateDiff){

            $result['averageScore'] = $overTimeScore ['average_score'];
            $result['reviewCount'] = $overTimeScore ['review_count'];

            if ($dateDiff->format("%a") < 30) {
                $result['dateGroup'] = $overTimeScore ['day_group']."-".$overTimeScore ['month_group']."-".$overTimeScore ['year_group'];
            } elseif ($dateDiff->format("%a") < 90) {
                $result['dateGroup'] = "Week: ".$overTimeScore ['week_group']." (".$overTimeScore ['year_group'].")";
            } else {
                $result['dateGroup'] = "Month: ".$overTimeScore ['month_group']." (".$overTimeScore ['year_group'].")";
            }
            return $result;
        }, $overTimeScoreList);

        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $jsonContent = $serializer->serialize($refinedOverTimeScoreList, 'json');
        $response = new Response($jsonContent);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}