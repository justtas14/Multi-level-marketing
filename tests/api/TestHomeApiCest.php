<?php namespace App\Tests;

use App\Entity\User;
use App\Tests\ApiTester;
use Doctrine\ORM\EntityManagerInterface;

class TestHomeApiCest
{
    public function _before(ApiTester $I)
    {
    }

    // Testing configuration api whether it returns serialized appropriate configuration object

    public function testConfigurationApi(ApiTester $I)
    {
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendGET('/api/configuration');

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $response = [
            'configuration' => [
                'landingContent' => '<h1>Prelaunch has ended!</h1>',
                'hasPrelaunchEnded' => false
            ]
        ];

        $I->canSeeResponseContainsJson($response);
    }
}
