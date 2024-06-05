<?php

namespace App\DataFixtures;

use App\Entity\Photo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PhotoFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $photo = new Photo();

        $photo->setName('test_photo');
        $photo->setPath('test_path.jpg');
        $photo->setCreatedAt(new \DateTimeImmutable());
        $photo->setUpdatedAt(new \DateTimeImmutable());
        $photo->setGallery($this->getReference('gallery_1'));
        $photo->setUser($this->getReference('user_1'));
        $manager->persist($photo);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            GalleryFixture::class,
            UserFixture::class,
        ];
    }
}
