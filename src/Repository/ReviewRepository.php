<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    private function getCommonQuery() {
        return $this->createQueryBuilder('r')
            ->select('ROUND(AVG(r.score), 1) as average_score, 
                COUNT(r.id) as review_count,
                YEAR(r.createdDate) AS year_group,
                MONTH(r.createdDate) AS month_group,
                WEEK(r.createdDate) AS week_group, 
                DAY(r.createdDate) AS day_group')
            ->where('r.hotelId = :hotelId')
            ->andwhere('r.createdDate >= :startDate')
            ->andWhere('r.createdDate <= :endDate')
            ->orderBy('r.createdDate')
            ->groupBy('year_group')
            ->addGroupBy('month_group')
        ;
    }
    /**
      * Returns an array of average scores by date group
     */
    public function getAverageScoreByHotel($id, $startDate, $endDate)
    {
        $commonQuery = $this->getCommonQuery();
        $dateDiff = $endDate->diff($startDate)->format("%a");

        if ($dateDiff < 90) {
            $commonQuery->addGroupBy('week_group');
            if ($dateDiff < 30) {
                $commonQuery->addGroupBy('day_group');
            }
        }

        return  $commonQuery
            ->setParameters(array('hotelId' => $id, 'startDate' => $startDate, 'endDate' => $endDate))
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * Returns an array of average scores by hotel
     */
    public function getAverageScore($startDate, $endDate)
    {
        return $this->createQueryBuilder('r')
            ->select(   'r.hotelId as hotel_id,
                ROUND(AVG(r.score), 1) as average_score, 
                COUNT(r.id) as review_count'
               )
            ->andwhere('r.createdDate >= :startDate')
            ->andWhere('r.createdDate <= :endDate')
            ->orderBy('average_score')
            ->groupBy('hotel_id')
            ->setParameters(array('startDate' => $startDate, 'endDate' => $endDate))
            ->getQuery()
            ->getResult()
            ;
    }
}
