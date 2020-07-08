<?php

namespace App\DataFixtures\TestFixtures;

use App\Entity\Review;
use Doctrine\Persistence\ObjectManager;

class TestReviewFixtures extends TestAppFixtures
{
    /** @var ObjectManager */
    private $manager;

    private function saveNewReview(Review $review)
    {
        $this->manager->persist($review);
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $randomReviewSet = array("Bad hotel. Stay away!",
            "Normal hotel, nothing surprising!",
            "New building, need more improvement!",
            "Fabulous, but so costly!",
            "Good service, friendly staffs",
            "Exceptional! Highly recommend"
        );

        $this->saveNewReview($this->createNewReview('1234', 8, $randomReviewSet[array_rand($randomReviewSet)],'2018-01-01'));
        $this->saveNewReview($this->createNewReview('1234', 6, $randomReviewSet[array_rand($randomReviewSet)],'2018-01-01'));

        $this->saveNewReview($this->createNewReview('1234', 5, $randomReviewSet[array_rand($randomReviewSet)],'2018-01-06'));
        $this->saveNewReview($this->createNewReview('1234', 7, $randomReviewSet[array_rand($randomReviewSet)],'2018-01-25'));

        $this->manager->flush();
    }
}
