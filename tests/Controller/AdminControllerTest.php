<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{

    public function testIndex()
    {
        $client = static::createClient();

        $userRepository = static::getContainer()->get('App\Repository\UserRepository');
        $user = $userRepository->findOneBy(['email' => 'test.admin@gmail.com']);

        $client->loginUser($user);
        $client->request('GET', '/admin/');

        $this->assertResponseRedirects('/admin/gallery');
    }

    public function testGallery()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get('App\Repository\UserRepository');
        $user = $userRepository->findOneBy(['email' => 'test.admin@gmail.com']);

        $client->loginUser($user);

        $client->request('GET', '/admin/gallery');

        $this->assertResponseIsSuccessful();
    }

    public function testGalleryShow()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get('App\Repository\UserRepository');
        $user = $userRepository->findOneBy(['email' => 'test.admin@gmail.com']);
        $client->loginUser($user);

        $galleryRepository = static::getContainer()->get('App\Repository\GalleryRepository');
        $gallery = $galleryRepository->findOneBy(['name' => 'test_gallery']);


        $client->request('GET', '/admin/gallery/' . $gallery->getId());

        $this->assertResponseIsSuccessful();
    }

    public function testAuthorization()
    {
        $client = static::createClient();
        $client->request('GET', '/admin/');

        $this->assertResponseRedirects('/login');
    }
}
