<?php

namespace App\DataFixtures\TestFixtures;

use App\Entity\Review;
use Doctrine\Persistence\ObjectManager;

class TestBenchmarkFixtures extends TestAppFixtures
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

        $this->saveNewReview($this->createNewReview('1', 9, $randomReviewSet[array_rand($randomReviewSet)],'2018-01-01'));
        $this->saveNewReview($this->createNewReview('2', 8.5, $randomReviewSet[array_rand($randomReviewSet)],'2018-01-01'));

        $this->saveNewReview($this->createNewReview('3', 8, $randomReviewSet[array_rand($randomReviewSet)],'2018-01-01'));
        $this->saveNewReview($this->createNewReview('4', 7.1, $randomReviewSet[array_rand($randomReviewSet)],'2018-01-01'));
        $this->saveNewReview($this->createNewReview('5', 6, $randomReviewSet[array_rand($randomReviewSet)],'2018-01-01'));
        $this->saveNewReview($this->createNewReview('6', 5, $randomReviewSet[array_rand($randomReviewSet)],'2018-01-01'));
        $this->saveNewReview($this->createNewReview('7', 4.9, $randomReviewSet[array_rand($randomReviewSet)],'2018-01-01'));
        $this->saveNewReview($this->createNewReview('8', 3.2, $randomReviewSet[array_rand($randomReviewSet)],'2018-01-01'));
        $this->saveNewReview($this->createNewReview('9', 2.6, $randomReviewSet[array_rand($randomReviewSet)],'2018-01-01'));
        $this->saveNewReview($this->createNewReview('10', 1, $randomReviewSet[array_rand($randomReviewSet)],'2018-01-01'));

        $this->manager->flush();
    }
}
