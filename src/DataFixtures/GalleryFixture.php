<?php

namespace App\DataFixtures;

use App\Entity\Gallery;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class GalleryFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $gallery = new Gallery();
        $gallery->setName('test_gallery');
        $gallery->setOrdering(1);
        $gallery->setCreatedAt(new \DateTimeImmutable());
        $gallery->setUpdatedAt(new \DateTimeImmutable());
        $gallery->setBackgroundPath('gallery1.jpg');
        $this->addReference('gallery_1', $gallery);
        $gallery->setUser($this->getReference('user_1'));
        $manager->persist($gallery);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
        ];
    }
}
