<?php

namespace App\Doctrine\DataFixtures;

use Faker\Generator;
use DateTimeImmutable;
use App\Model\Entity\Tag;
use App\Model\Entity\User;
use App\Model\Entity\Review;
use App\Model\Entity\VideoGame;
use function array_fill_callback;
use App\Rating\CountRatingsPerValue;
use App\Rating\CalculateAverageRating;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

final class VideoGameFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly Generator $faker,
        private readonly CalculateAverageRating $calculateAverageRating,
        private readonly CountRatingsPerValue $countRatingsPerValue
    ) {
    }

    public const KEYWORDS = ['multijoueur', 'solo', 'en ligne', 'hors ligne', 'coopératif', 'compétitif'];

    public function load(ObjectManager $manager): void
    {
        $users = $manager->getRepository(User::class)->findAll();

        $tags = [];

        // Création des tags
        foreach (self::KEYWORDS as $keyword) {
            $tag = (new Tag())->setName($keyword);
            $manager->persist($tag);
            $this->addReference('tag_' . $keyword, $tag);
            $tags[] = $tag;
        }

        $videoGames = [];
        // $videoGames = array_fill_callback(0, 50, fn (int $index): VideoGame => (new VideoGame)
        for($i = 0; $i < 50; $i++){
        $videoGame = (new VideoGame())
            ->setTitle(sprintf('Jeu vidéo %d', $i))
            ->setDescription($this->faker->paragraphs(10, true))
            ->setReleaseDate(new DateTimeImmutable())
            ->setTest($this->faker->paragraphs(6, true))
            ->setRating(($i % 5) + 1)
            ->setImageName(sprintf('video_game_%d.png', $i))
            ->setImageSize(2_098_872);

            $randomTags = $this->faker->randomElements($tags, rand(1, 3));
            // Ajout aléatoire de tags
            foreach ($randomTags as $tag) {
                $videoGame->addTag($tag);
            }

            $manager->persist($videoGame);

            $videoGames[] = $videoGame;
        };


        // TODO : Ajouter les tags aux vidéos

        // array_walk($videoGames, [$manager, 'persist']);

        $manager->flush();

        // TODO : Ajouter des reviews aux vidéos
        for ($i = 0; $i < 75; $i++){
            $review = (new Review())
                ->setUser($this->faker->randomElement($users))
                ->setVideoGame($this->faker->randomElement($videoGames))
                ->setRating($this->faker->numberBetween(1, 5))
                ->setComment($this->faker->paragraphs(1, true));

            $this->calculateAverageRating->calculateAverage($videoGame);
            $this->countRatingsPerValue->countRatingsPerValue($videoGame);
    


            $manager->persist($review);

        }

        $manager->flush();


    }

    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }
}
