<?php

namespace App\DataFixtures;

use App\Entity\Asset;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AssetFixture extends Fixture implements DependentFixtureInterface
{
    /** @var UserPasswordEncoderInterface  */
    private  $encoder;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $em)
    {
        $this->encoder = $encoder;
        $this->em = $em;
    }

    public function load(ObjectManager $manager)
    {

        /** @var UserRepository $usrtRerp */
        $usrtRerp = $this->em->getRepository(User::class);
        $user  = $usrtRerp->findFirst();
        $asset = (new Asset())
            ->setUser($user)
            ->setValue(1)
            ->setLabel('test asset')
            ->setCurrency('BTC');
        $manager->persist($asset);

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}
