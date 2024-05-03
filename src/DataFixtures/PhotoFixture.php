<?php

namespace App\DataFixtures;

use App\Entity\Photo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PhotoFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $photo = new Photo();

        $photo->setName('test_photo');
        $photo->setPath('test_path.jpg');
        $photo->setCreatedAt(new \DateTimeImmutable());
        $photo->setUpdatedAt(new \DateTimeImmutable());
        $photo->setGallery($this->getReference('gallery_1'));
        $manager->persist($photo);

        $manager->flush();
    }
}
