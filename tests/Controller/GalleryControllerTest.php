<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\File;

class GalleryControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/gallery/');
        $this->assertResponseIsSuccessful();
    }

    public function testShow()
    {
        $client = static::createClient();
        $galleryRepository = static::getContainer()->get('App\Repository\GalleryRepository');
        $gallery = $galleryRepository->findOneBy(['name' => 'test_gallery']);
        $client->request('GET', '/gallery/' . $gallery->getId());
        $this->assertResponseIsSuccessful();
    }

    public function testCreate()
    {

        $client = static::createClient();
        $client->disableReboot(); 

        //Mock gallery service method : saveBackgroundImgFile

        $container = static::getContainer();
        $galleryServiceMock = $this->getMockBuilder('App\Service\GalleryService')
            ->disableOriginalConstructor()
            ->getMock();
        $galleryServiceMock->method('saveBackgroundImgFile')->willReturn('public/test_assets/background.jpg');
        $container->set('App\Service\GalleryService', $galleryServiceMock);

        $userRepository = static::getContainer()->get('App\Repository\UserRepository');
        $user = $userRepository->findOneBy(['email' => 'test.admin@gmail.com']);
        $client->loginUser($user);

        $crawler = $client->request('GET', '/gallery/create');
        $this->assertResponseIsSuccessful();





        $buttonCrawlerNode = $crawler->selectButton('Enregistrer');

        $form = $buttonCrawlerNode->form();

        $form['gallery[name]'] = 'test_gallery_create';
        $form['gallery[ordering]'] = 10;
        $form['gallery[background]']->upload('public/test_assets/background.jpg');
        $crawler = $client->submit($form);


        $galleryRepository = static::getContainer()->get('App\Repository\GalleryRepository');
        $gallery = $galleryRepository->findOneBy(['name' => 'test_gallery_create']);
        $this->assertNotNull($gallery);

        $this->assertResponseRedirects('/admin/gallery/' . $gallery->getId());


    }

    public function testEdit()
    {
        $client = static::createClient();
        $client->disableReboot(); //Some dark magic found on stackoverflow

        //Mock gallery service method : saveBackgroundImgFile

        $container = static::getContainer();
        $galleryServiceMock = $this->getMockBuilder('App\Service\GalleryService')
            ->disableOriginalConstructor()
            ->getMock();
        $galleryServiceMock->method('saveBackgroundImgFile')->willReturn('public/test_assets/background.jpg');
        $container->set('App\Service\GalleryService', $galleryServiceMock);

        $userRepository = static::getContainer()->get('App\Repository\UserRepository');
        $user = $userRepository->findOneBy(['email' => 'test.admin@gmail.com']);
        $client->loginUser($user);

        $galleryRepository = static::getContainer()->get('App\Repository\GalleryRepository');
        $gallery = $galleryRepository->findOneBy(['name' => 'test_gallery']);

        $crawler = $client->request('GET', '/gallery/edit/' . $gallery->getId());
        $this->assertResponseIsSuccessful();

        $buttonCrawlerNode = $crawler->selectButton('Enregistrer');

        $form = $buttonCrawlerNode->form();

        $form['gallery[name]'] = 'test_gallery_edit';
        $form['gallery[ordering]'] = 10;
        $form['gallery[background]']->upload('public/test_assets/background.jpg');
        $crawler = $client->submit($form);

        $gallery = $galleryRepository->findOneBy(['name' => 'test_gallery_edit']);
        $this->assertNotNull($gallery);

        $this->assertResponseRedirects('/admin/gallery');

    }

    public function testDelete()
    {
        $client = static::createClient();
        $client->disableReboot(); //Some dark magic found on stackoverflow

        //Mock FileUtil method : removeFile

        $container = static::getContainer();
        $fileUtilMock = $this->getMockBuilder('App\Util\FileUtil')
            ->disableOriginalConstructor()
            ->getMock();

        $container->set('App\Util\FileUtil', $fileUtilMock);


        $userRepository = static::getContainer()->get('App\Repository\UserRepository');
        $user = $userRepository->findOneBy(['email' => 'test.admin@gmail.com']);
        $client->loginUser($user);

        $galleryRepository = static::getContainer()->get('App\Repository\GalleryRepository');
        $gallery = $galleryRepository->findOneBy(['name' => 'test_gallery']);

        $client->request('GET', '/gallery/delete/' . $gallery->getId());
        $this->assertResponseRedirects('/admin/gallery');

        $gallery = $galleryRepository->findOneBy(['name' => 'test_gallery']);
        $this->assertNull($gallery);

    }

    public function testCreatePhoto()
    {
        $client = static::createClient();
        $client->disableReboot(); //Some dark magic found on stackoverflow

        //Mock FileUtil method : uploadFile

        $container = static::getContainer();
        $fileUtilMock = $this->getMockBuilder('App\Util\FileUtil')
            ->disableOriginalConstructor()
            ->getMock();
        $fileUtilMock->method('uploadFile')->willReturn("public/test_assets/photo.jpg");
        $container->set('App\Util\FileUtil', $fileUtilMock);

        $userRepository = static::getContainer()->get('App\Repository\UserRepository');
        $user = $userRepository->findOneBy(['email' => 'test.admin@gmail.com']);
        $client->loginUser($user);

        $galleryRepository = static::getContainer()->get('App\Repository\GalleryRepository');

        $gallery = $galleryRepository->findOneBy(['name' => 'test_gallery']);
        $crawler = $client->request('GET', '/gallery/' . $gallery->getId() . '/photo/create');
        $this->assertResponseIsSuccessful();

        $buttonCrawlerNode = $crawler->selectButton('Enregistrer');

        $form = $buttonCrawlerNode->form();

        $form['photo[name]'] = 'test_photo';
        $form['photo[description]'] = "test_description";
        $form['photo[img]']->upload('public/test_assets/background.jpg');
        $crawler = $client->submit($form);

        $photoRepository = static::getContainer()->get('App\Repository\PhotoRepository');
        $photo = $photoRepository->findOneBy(['name' => 'test_photo']);
        $this->assertNotNull($photo);

        $gallery = $galleryRepository->findOneBy(['name' => 'test_gallery']);
        $this->assertEquals($photo->getGallery()->getId(), $gallery->getId());

        $this->assertResponseRedirects('/admin/gallery/' . $gallery->getId());
    }

    public function testAuthorize()
    {
        $client = static::createClient();
        $client->disableReboot(); //Some dark magic found on stackoverflow

        $userRepository = static::getContainer()->get('App\Repository\UserRepository');
        $user = $userRepository->findOneBy(['email' => 'test.user@gmail.com']);
        $client->loginUser($user);

        $galleryRepository = static::getContainer()->get('App\Repository\GalleryRepository');
        $gallery = $galleryRepository->findOneBy(['name' => 'test_gallery']);

        $client->request('GET', '/gallery/edit/' . $gallery->getId());
        $this->assertResponseStatusCodeSame(403);

        $client->request('GET', '/gallery/delete/' . $gallery->getId());
        $this->assertResponseStatusCodeSame(403);

        $client->request('GET', '/gallery/' . $gallery->getId() . '/photo/create');
        $this->assertResponseStatusCodeSame(403);

        $client->request('GET', '/gallery/create');
        $this->assertResponseStatusCodeSame(403);

    }

}
