<?php


namespace App\Tests\Acceptance;

use App\Tests\Support\AcceptanceTester;

class SigninCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function signInSuccessfully(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->fillField("email", 'admin@test.com');
        $I->fillField('password', 'qwerty');
        $I->click('Connexion');
        $I->see('Hello, davert');
    }
}
