<?php

namespace App\DataFixtures;

use App\Entity\Review;
use App\Entity\Hotel;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ReviewFixtures extends AppFixtures implements DependentFixtureInterface
{
	public function getDependencies()
    {
        return array(
            HotelFixtures::class,
        );
    }

    public function loadData(ObjectManager $manager)
    {	
    	$hotelRepository = $manager->getRepository(Hotel::class);
    	$hotelSet = $hotelRepository->findAll();

        $randomReviewSet = array("Bad hotel. Stay away!",
            "Normal hotel, nothing surprising!",
            "New building, need more improvement!",
            "Fabulous, but so costly!",
            "Good service, friendly staffs",
            "Exceptional! Highly recommend"
        );

        $this->createMany(Review::class, 100000, function(Review $review, $hotelSet, $randomReviewSet) {

            $randomHotel = $hotelSet[array_rand($hotelSet)];
			$review->setHotelId($randomHotel->getId());

			$review->setScore(mt_rand(10, 1000) / 100);

			$review->setComment($randomReviewSet[array_rand($randomReviewSet)]);

			$currentDate = new \DateTime();
			$currentDateTimestamp = $currentDate->getTimestamp();
            $lastTwoYearTimestamp = $currentDate->modify('-2 years')->getTimestamp();
            $randomTimestamp = mt_rand($lastTwoYearTimestamp, $currentDateTimestamp);
            $randomDate = new \DateTime();
            $randomDate->setTimestamp($randomTimestamp);
			$review->setCreatedDate($randomDate);

        }, $hotelSet, $randomReviewSet);
    }
}
