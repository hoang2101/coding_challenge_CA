<?php

namespace App\DataFixtures\TestFixtures;

use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TestAppFixtures extends Fixture
{
    /** @var ObjectManager */
    private $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->loadData($manager);
    }

    protected function createNewReview(int $hotelId, int $score, string $comment, string $Datetime)
    {
        $review = new Review();
        $review->setHotelId($hotelId);
        $review->setScore($score);
        $review->setComment($comment);
        $review->setCreatedDate(\DateTime::createFromFormat('Y-m-d', $Datetime));
        return $review;
    }
}
