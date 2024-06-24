<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PhotoControllerTest extends WebTestCase
{

    public function testEdit()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get('App\Repository\UserRepository');
        $user = $userRepository->findOneBy(['email' => 'test.admin@gmail.com']);
        $client->loginUser($user);

        //Get the first photo from the database
        $photoRepository = static::getContainer()->get('App\Repository\PhotoRepository');
        $photo = $photoRepository->findOneBy(['name' => 'test_photo']);


        $client->request('GET', '/photo/edit/' . $photo->getId());
        $this->assertResponseIsSuccessful();

        $client->submitForm('Modifier', [
            'photo[name]' => 'test_photo_edited',
            'photo[description]' => 'test_description_edited',
        ]);
        $this->assertResponseRedirects('/admin/gallery/' . $photo->getGallery()->getId());

        $photo = $photoRepository->findOneBy(['name' => 'test_photo_edited']);
        $this->assertNotNull($photo);
        $this->assertEquals('test_description_edited', $photo->getDescription());
    }

    public function testDelete()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get('App\Repository\UserRepository');
        $user = $userRepository->findOneBy(['email' => 'test.admin@gmail.com']);
        $client->loginUser($user);

        //Get the first photo from the database
        $photoRepository = static::getContainer()->get('App\Repository\PhotoRepository');
        $photo = $photoRepository->findOneBy(['name' => 'test_photo']);

        $client->request('GET', '/photo/delete/' . $photo->getId());
        $this->assertResponseRedirects('/admin/gallery/' . $photo->getGallery()->getId());

        $photo = $photoRepository->findOneBy(['name' => 'test_photo']);
        $this->assertNull($photo);


    }

    public function testAuthorisation()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get('App\Repository\UserRepository');
        $user = $userRepository->findOneBy(['email' => 'test.user@gmail.com']);
        $client->loginUser($user);
        $photoRepository = static::getContainer()->get('App\Repository\PhotoRepository');
        $photo = $photoRepository->findOneBy(['name' => 'test_photo']);
        $client->request('GET', '/photo/edit/' . $photo->getId());
        $this->assertResponseStatusCodeSame(403);
        $client->request('GET', '/photo/delete/' . $photo->getId());
        $this->assertResponseStatusCodeSame(403);
    }
}
