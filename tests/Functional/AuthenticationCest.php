<?php


namespace App\Tests\Functional;

use App\Tests\Support\FunctionalTester;

class AuthenticationCest
{
    public function _before(FunctionalTester $I)
    {
    }

    // tests
    public function backofficeIsAccessibleWhenAdmin(FunctionalTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField(['name' => 'email'], 'test.admin@gmail.com');
        $I->fillField(['name' => 'password'], 'password');
        $I->click('Connexion', 'button');
        $I->see('Retour au site');

        $I->amOnPage('/admin');
        $I->see('Retour au site');
    }

    public function backofficeIsNotAccessibleWhenUser(FunctionalTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField(['name' => 'email'], 'test.user@gmail.com');
        $I->fillField(['name' => 'password'], 'password');
        $I->click('Connexion', 'button');
        $I->see('Déconnexion');

        $I->amOnPage('/admin');
        $I->see('Access Denied');

        $I->amOnPage('/logout');
        $I->see('Connexion');

        $I->amOnPage('/admin');
        $I->see('Veuillez-vous connecter');
    }
}
