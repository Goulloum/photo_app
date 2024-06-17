<?php


namespace App\Tests\Acceptance;

use App\Tests\Support\AcceptanceTester;

class NavigationMobileCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function logoRedirectToHome(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->click('.navbar-title', '.navbar-container-mobile');
        $I->seeInCurrentUrl('/gallery');
    }

    public function galleryLink(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->click('Galeries', '.navbar-container-mobile');
        $I->seeInCurrentUrl('/gallery');
    }

    public function eventLink(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->click('Événements', '.navbar-container-mobile');
        $I->seeInCurrentUrl('/event');
    }

    public function contactLink(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->click('Contact', '.navbar-container-mobile');
        $I->seeInCurrentUrl('/contact');
    }

    public function loginLink(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->click('Connexion', '.navbar-container-mobile');
        $I->seeInCurrentUrl('/login');
    }
}
