<?php


namespace App\Tests\Acceptance;

use App\Tests\Support\AcceptanceTester;

class NavigationDesktopCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function logoRedirectToHome(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->click('.navbar-title');
        $I->seeInCurrentUrl('/gallery');
    }

    public function galleryLink(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->click('Galeries');
        $I->seeInCurrentUrl('/gallery');
    }

    public function eventLink(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->click('Événements');
        $I->seeInCurrentUrl('/event');
    }

    public function contactLink(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->click('Contact');
        $I->seeInCurrentUrl('/contact');
    }

    public function loginLink(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->click('Connexion');
        $I->seeInCurrentUrl('/login');
    }
}
