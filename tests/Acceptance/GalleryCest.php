<?php


namespace App\Tests\Acceptance;

use App\Tests\Support\AcceptanceTester;

class GalleryCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function viewGallery(AcceptanceTester $I)
    {
        $I->amOnPage('/gallery');
        $I->see('Galerie');

        $I->click('Test Gallery');
        $I->see('Test Gallery');
    }
}
