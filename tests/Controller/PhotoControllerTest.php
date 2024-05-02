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

        $client->request('GET', '/admin/photo/1/edit');
    }
}
