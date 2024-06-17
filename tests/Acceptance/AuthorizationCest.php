<?php


namespace App\Tests\Acceptance;

use App\Tests\Support\AcceptanceTester;

class AuthorizationCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function backofficeIsAccessibleWhenAdmin(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField(['name' => 'email'], 'test.admin@gmail.com');
        $I->fillField(['name' => 'password'], 'password');
        $I->click('Connexion', 'button');
        $I->see('Retour au site');
    }

    public function backofficeIsNotAccessibleWhenUser(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField(['name' => 'email'], 'test.user@gmail.com');
        $I->fillField(['name' => 'password'], 'password');
        $I->click('Connexion', 'button');
        $I->see('Déconnexion');
        $I->amOnPage('/admin');
        $I->see('Access Denied');
    }

    public function backofficeIsNotAccessibleWhenNotLogged(AcceptanceTester $I)
    {
        $I->amOnPage('/admin');
        $I->see('Veuillez-vous connecter');
    }
}
