<?php


namespace App\Tests\Acceptance;

use App\Tests\Support\AcceptanceTester;

class SigninCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function signinSuccesfullyUser(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField(['name' => 'email'], 'test.user@gmail.com');
        $I->fillField(['name' => 'password'], 'password');
        $I->click('Connexion', 'button');
        $I->see('Déconnexion');
    }

    public function signinSuccesfullyAdmin(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField(['name' => 'email'], 'test.admin@gmail.com');
        $I->fillField(['name' => 'password'], 'password');
        $I->click('Connexion', 'button');
        $I->see('Retour au site');
    }

    public function signinFailed(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField(['name' => 'email'], 'sbugsebgse');
        $I->fillField(['name' => 'password'], 'password');
        $I->click('Connexion', 'button');
        $I->see('Invalid credentials.');
    }
}
