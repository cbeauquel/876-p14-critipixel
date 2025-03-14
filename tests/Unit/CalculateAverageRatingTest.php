<?php

namespace Tests\Unit;

use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use App\Rating\RatingHandler;
use PHPUnit\Framework\TestCase;

class CalculateAverageRatingTest extends TestCase
{
    public function testAverageRatingCalculNoReview(): void
    {
        $ratingHandler = new RatingHandler();

        $videoGame = new VideoGame();
        $ratingHandler->calculateAverage($videoGame);
        $this->assertNull($videoGame->getAverageRating());
    }

    public function testCalculateAverageReviews(): void
    {
        $ratingHandler = new RatingHandler();

        $videoGame = new VideoGame();

        $review1 = (new Review())->setRating(4);
        $review2 = (new Review())->setRating(5);
        $review3 = (new Review())->setRating(3);

        $videoGame->getReviews()->add($review1);
        $videoGame->getReviews()->add($review2);
        $videoGame->getReviews()->add($review3);

        $ratingHandler->calculateAverage($videoGame);
        $this->assertEquals(4, $videoGame->getAverageRating());
    }
}
