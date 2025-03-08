<?php

namespace Tests\Unit;

use App\Model\Entity\Review;
use App\Rating\RatingHandler;
use App\Model\Entity\VideoGame;
use PHPUnit\Framework\TestCase;

class CountRatingPerValueTest extends TestCase
{
    public function testCountRatingsPerValueNoReviews(): void
    {
        $videoGame = new VideoGame();
        $ratingHandler = new RatingHandler;

        $ratingHandler->countRatingsPerValue($videoGame);

        $this->assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfOne());
        $this->assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfTwo());
        $this->assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfThree());
        $this->assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfFour());
        $this->assertEquals(0, $videoGame->getNumberOfRatingsPerValue()->getNumberOfFive());
    }


    /**
     * @dataProvider provideRatingsData
     */
    public function testCountRatingsPerValueReviews(array $ratings, int $expectedOne, int $expectedTwo, int $expectedThree, int $expectedFour, int $expectedFive): void
    {
        $videoGame = new VideoGame();
        $ratingHandler = new RatingHandler;

        foreach ($ratings as $rating) {
            $videoGame->getReviews()->add((new Review())->setRating($rating));
        }

        $ratingHandler->countRatingsPerValue($videoGame);

        $this->assertEquals($expectedOne, $videoGame->getNumberOfRatingsPerValue()->getNumberOfOne());
        $this->assertEquals($expectedTwo, $videoGame->getNumberOfRatingsPerValue()->getNumberOfTwo());
        $this->assertEquals($expectedThree, $videoGame->getNumberOfRatingsPerValue()->getNumberOfThree());
        $this->assertEquals($expectedFour, $videoGame->getNumberOfRatingsPerValue()->getNumberOfFour());
        $this->assertEquals($expectedFive, $videoGame->getNumberOfRatingsPerValue()->getNumberOfFive());
    }

    public function provideRatingsData(): array
    {
        return [
            'exemple 1' => [
                'ratings' => [1, 3, 5, 5],
                'expectedOne' => 1,
                'expectedTwo' => 0,
                'expectedThree' => 1,
                'expectedFour' => 0,
                'expectedFive' => 2,
            ],
            'exemple 2' => [
                'ratings' => [2, 2, 4, 4, 4],
                'expectedOne' => 0,
                'expectedTwo' => 2,
                'expectedThree' => 0,
                'expectedFour' => 3,
                'expectedFive' => 0,
            ],
            'exemple 3' => [
                'ratings' => [1, 2, 3, 4, 5],
                'expectedOne' => 1,
                'expectedTwo' => 1,
                'expectedThree' => 1,
                'expectedFour' => 1,
                'expectedFive' => 1,
            ],
            'exemple 4' => [
                'ratings' => [5, 5, 5, 5, 5],
                'expectedOne' => 0,
                'expectedTwo' => 0,
                'expectedThree' => 0,
                'expectedFour' => 0,
                'expectedFive' => 5,
            ],
        ];
    }
}
