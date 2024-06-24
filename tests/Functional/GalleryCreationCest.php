<?php


namespace App\Tests\Functional;

use App\Tests\Support\FunctionalTester;
use Codeception\Attribute\After;

class GalleryCreationCest
{
    public function _before(FunctionalTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField(['name' => 'email'], 'test.admin@gmail.com');
        $I->fillField(['name' => 'password'], 'password');
        $I->click('Connexion', 'button');
    }

    // tests
    public function createGallery(FunctionalTester $I)
    {
        $I->amOnPage('/gallery/create');
        $I->see('Ajouter une galerie');

        $I->fillField(['name' => 'gallery[name]'], 'Test Gallery');
        $I->fillField(['name' => 'gallery[ordering]'], '10');
        $I->fillField(['name' => 'gallery[backgroundXOffset]'], '0');
        $I->fillField(['name' => 'gallery[backgroundYOffset]'], '0');
        $I->attachFile('gallery[background]', 'logo_transparent.png');
        $I->click('Enregistrer', 'button');

        $I->see('La galerie a bien été créée');
        $I->see('Test Gallery');
    }

    // #[After('createGallery')]
    // public function deleteGallery(FunctionalTester $I)
    // {
    //     $I->amOnPage('/admin/gallery');
    //     $I->see('Test Gallery');
    //     //Click the button delete on the line with the first td is the name of the previously created gallery
    //     $I->click('Supprimer', '//tr[td[1]//a[contains(text(),"Test Gallery")]]/td[7]');
    //     $I->see('La galerie a bien été supprimée');
    // }
}
