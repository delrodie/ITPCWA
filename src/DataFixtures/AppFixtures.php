<?php

namespace App\DataFixtures;

use _PHPStan_582a9cb8b\Nette\Utils\DateTime;
use App\Entity\EnInfo;
use App\Entity\FrInfo;
use App\Entity\FrType;
use App\Entity\Slide;
use App\Entity\User;
use App\Services\GestionCache;
use App\Services\Utility;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{


    public function __construct(
        private GestionCache $gestionCache, private Utility $utility
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {

        // Utilisateur
        $user = new User();
        $user->setEmail('delrodieamoikon@gmail.com');
        $user->setPassword('$2y$13$9z9GxsRa3NV2As1MvCJUFeiCl0.kebWi9DZL5o1KzGNCce3KV1/Zm');
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        $manager->persist($user);

        // Message francais
        $fakerFr = Factory::create('fr_FR');
        for ($i=1; $i<=5; $i++){
            $fin = 'P'.$i.'D';
            $frInfo = new FrInfo();
            $frInfo->setTitre($fakerFr->text(50));
            $frInfo->setDebut(new \DateTime());
            $frInfo->setFin(new \DateTime('now +'.$i.' day'));
            $frInfo->setStatut(true);
            $manager->persist($frInfo);
        }

        // Message anglais
        $faker = Factory::create('en_US');
        for ($i=1; $i<=5; $i++){
            $j=$i+1;
            $enInfo = new EnInfo();
            $enInfo->setTitre($faker->text(50));
            $enInfo->setDebut(new \DateTime());
            $enInfo->setFin(new \DateTime('now +'.$j.' day'));
            $enInfo->setStatut(true);
            $manager->persist($enInfo);
        }

        // Slider
        for ($i=1; $i<=3; $i++){
            $slide = new Slide();
            //$slide->setMedia($fakerFr->imageUrl(1920, 1080, 'people', true, 'slide', false, 'png' ));
            $slide->setTitre($fakerFr->sentence(4));
            $slide->setStatut(true);
            $this->utility->slug($slide, 'slide');
            $manager->persist($slide);
        }

        // Type de presentation francais
        for ($i=1; $i<=5; $i++){
            $frType = new FrType();
            $frType->setTitre($fakerFr->sentence(2));
            $this->utility->slug($frType, 'frType');
            $manager->persist($frType);
        }

        $manager->flush();
        // Symfony console d:f:l

        $this->gestionCache->cacheSlides();
        $this->gestionCache->cacheEnMessage();
        $this->gestionCache->cacheFrMessages();
    }
}
