<?php namespace App\Tests;

use App\Entity\Associate;
use App\Entity\Configuration;
use App\Entity\EmailTemplate;
use App\Entity\File;
use App\Entity\Gallery;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class TestAdminApiCest
{
    private $token;

    public function _before(ApiTester $I)
    {
        /** @var EntityManagerInterface $em */
        $em = $I->getEntityManager();

        /** @var User $user */
        $user = $em->getRepository(User::class)->find(404);
        $this->token = $I->authenticate($user);
    }

    // Testing /api/admin/emailtemplate/{type} with various types in path params. Checking whether appropriate
    // email template is updated depending on given post request params.
    // Also checking expected errors for incorect given params.

    public function testEmailFormSubmitApi(ApiTester $I)
    {
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPOST(
            '/api/admin/emailtemplate/invitation',
            [
                'emailSubject' => 'kazkoks tekstas',
                'emailBody' => '<h3><br/> Here is your <a href=\'{{link}}\'>link</a></h3>'
            ]
        );
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $emailBodyFromJson = $I->grabDataFromResponseByJsonPath('*.emailBody')[0];
        $emailSubjectFromJson = $I->grabDataFromResponseByJsonPath('*.emailSubject')[0];

        $response = [
            'formSuccess' => true,
            'formError' => '',
            'emailTemplate' => [
                'emailSubject' => 'kazkoks tekstas',
                'emailBody' => '<h3><br/> Here is your <a href=\'{{link}}\'>link</a></h3>'
            ]
        ];

        $I->canSeeResponseContainsJson($response);

        $I->seeInRepository(EmailTemplate::class, [
            'emailSubject' => $emailSubjectFromJson,
            'emailBody' => $emailBodyFromJson
        ]);

        $I->sendPOST(
            '/api/admin/emailtemplate/invitation',
            [
                'emailSubject' => 'You got invited by {{senderName}}!!!',
                'emailBody' => ''
            ]
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $emailBodyFromJson = $I->grabDataFromResponseByJsonPath('*.emailBody')[0];
        $emailSubjectFromJson = $I->grabDataFromResponseByJsonPath('*.emailSubject')[0];

        $response = [
            'formSuccess' => false,
            'formError' => 'Please do not leave empty values',
            'emailTemplate' => [
                'emailSubject' => 'kazkoks tekstas',
                'emailBody' => '<h3><br/> Here is your <a href=\'{{link}}\'>link</a></h3>'
            ]
        ];

        $I->canSeeResponseContainsJson($response);

        $I->seeInRepository(EmailTemplate::class, [
            'emailSubject' => $emailSubjectFromJson,
            'emailBody' => $emailBodyFromJson
        ]);
    }

    /**
     * @param \App\Tests\ApiTester $I
     * @after testEmailFormSubmitApi
     */

    // Testing /api/admin api whether ir returns object with appropriate information for admin home page.

    public function testAdminHomeApi(ApiTester $I)
    {
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendGET('/api/admin');

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $response = [
            'maxLevel' => 6,
            'levels' => 5,
            'associatesInLevels' => [
                '1' => 2,
                '2' => 4,
                '3' => 6,
                '4' => 5,
                '5' => 4,
            ]
        ];

        $I->canSeeResponseContainsJson($response);
    }

    // Testing /api/admin/changecontent with given params of test gallery file ids.
    // Expected in database update of termsOfServices and mainLogo files with given file with post param id.
    // Also expected for tosDiscaimer to be updated in database and be found in JSON object.

    public function testChangeContentApi(ApiTester $I)
    {
        /** @var EntityManagerInterface $em */
        $em = $I->getEntityManager();

        $I->loadPictures();

        /** @var Gallery $galleryFile */
        $galleryFile = $em->getRepository(Gallery::class)->findAll()['0'];

        $id = $galleryFile->getId();

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPOST(
            '/api/admin/changecontent',
            [
                'hiddenMainLogoFile' => $id,
                'hiddenTermsOfServiceFile' => $id,
                'tosDisclaimer' => 'some disclaimer'
            ]
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $configuration = $I->grabDataFromResponseByJsonPath('.configuration')[0];

        $configurationEntity = $em->getRepository(Configuration::class)->findAll();
        /** @var File $mainLogo */
        $mainLogo = $configurationEntity['0']->getMainLogo();
        $termsOfServices = $configurationEntity['0']->getTermsOfServices();

        $response = [
            'configuration' => [
                'tosDisclaimer' => 'some disclaimer',
                'mainLogo' => $configuration['mainLogo'],
                'termsOfServices' => $configuration['termsOfServices']
            ],
            'contentChanged' => true,
        ];

        $I->canSeeResponseContainsJson($response);

        $I->seeInRepository(Configuration::class, [
            'mainLogo' => $mainLogo,
            'termsOfServices' => $termsOfServices,
            'tosDisclaimer' => $configuration['tosDisclaimer'],
        ]);
    }

    // Testing /api/admin/get-logs api whether it returns appropriate loaded logs in JSON.

    public function testLogs(ApiTester $I)
    {
        /** @var EntityManagerInterface $em */
        $em = $I->getEntityManager();

        $I->loadLogs();

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendGET(
            '/api/admin/get-logs',
            ['page' => 'saf']
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::NOT_FOUND);

        $I->sendGET(
            '/api/admin/get-logs',
            ['page' => 1]
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $response = [
            'logs' => [
                '0' => [
                    'message' => 'Profile Updated!'
                ],
                '4' => [
                    'message' => 'Profile Updated!'
                ],
                '6' => [
                    'message' => 'Prelaunch ended!'
                ]
            ],
            'pagination' => [
                'maxPage' => 1,
                'currentPage' => 1
            ]
        ];

        $I->canSeeResponseContainsJson($response);

        $response = [
            'logs' => [
                '7' => [
                    'message' => 'Prelaunch Ended!'
                ]
            ],
            'pagination' => [
                'maxPage' => 1,
                'currentPage' => 1
            ]
        ];
        $I->cantSeeResponseContainsJson($response);
    }

    // Testing /api/admin/user api whether it updates associate parent
    // or deletes associate with appropriate post params.
    // Also expected for errors when incorrect post params are given.

    public function testUsers(ApiTester $I)
    {
        /** @var EntityManagerInterface $em */
        $em = $I->getEntityManager();

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        /** @var Associate $associate */
        $associateParent = $em->getRepository(Associate::class)->find(1);

        $I->sendPOST(
            '/api/admin/users',
            ['associateId' => $associateParent->getId()]
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $response = [
            'associate' => [
                'fullName' => "Connor Vaughan",
                'email' => 'admin@plumtreesystems.com',
                'roles' => [
                    '0' => 'ROLE_ADMIN',
                    '1' => 'ROLE_USER'
                ]
            ],
            'associateParent' => null,
        ];

        $I->canSeeResponseContainsJson($response);

        /** @var Associate $associate */
        $associate = $em->getRepository(Associate::class)->find(4);

        $I->sendPOST(
            '/api/admin/users',
            ['associateId' => $associate->getId(), 'associateParentId' => $associateParent->getId()]
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $response = [
            'associate' => [
                'fullName' => 'Bailey Brookes',
                'email' => 'BaileyBrookes@dayrep.com',
                'roles' => [
                    '0' => 'ROLE_USER'
                ]

            ],
            'associateParent' => [
                'fullName' => "Connor Vaughan",
                'email' => 'admin@plumtreesystems.com',
                'roles' => [
                    '0' => 'ROLE_ADMIN',
                    '1' => 'ROLE_USER'
                ]
            ],
        ];

        $I->canSeeResponseContainsJson($response);

        $I->seeInRepository(Associate::class, [
            'email' => 'BaileyBrookes@dayrep.com',
            'parentId' => $associateParent->getId()
        ]);

        $I->sendPOST(
            '/api/admin/users',
            ['associateId' => $associate->getId(), 'deleteAssociateId' => $associateParent->getId()]
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $response = [
            'formError' => 'You cannot delete yourself',
            'associate' => [
                'fullName' => 'Bailey Brookes',
                'email' => 'BaileyBrookes@dayrep.com',
                'roles' => [
                    '0' => 'ROLE_USER'
                ]

            ],
            'associateParent' => [
                'fullName' => "Connor Vaughan",
                'email' => 'admin@plumtreesystems.com',
                'roles' => [
                    '0' => 'ROLE_ADMIN',
                    '1' => 'ROLE_USER'
                ]
            ],
        ];

        $I->canSeeResponseContainsJson($response);

        $I->canSeeInRepository(Associate::class, [
            'email' => 'admin@plumtreesystems.com',
        ]);

        $deleteAssociateFullName = $associate->getFullName();

        $I->sendPOST(
            '/api/admin/users',
            ['associateId' => $associateParent->getId(), 'deleteAssociateId' => $associate->getId()]
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $response = [
            'formError' => '',
            'formSuccess' => [
                'message' => 'User ' .$deleteAssociateFullName. ' deleted',
                'type' => "delete",
            ]
        ];

        $I->canSeeResponseContainsJson($response);

        $I->cantSeeInRepository(Associate::class, [
            'email' => 'BaileyBrookes@dayrep.com',
        ]);

        /** @var Associate $associate */
        $associate = $em->getRepository(Associate::class)->find(3);

        $I->sendPOST(
            '/api/admin/users',
            ['associateId' => $associate->getId(), 'deleteAssociateId' => $associate->getId()]
        );

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $response = [
            'formError' => 'Cannot delete user with children',
            'associate' => [
                'fullName' => 'Sarah Cunningham',
                'email' => 'SarahCunningham@dayrep.com',
                'roles' => [
                    '0' => 'ROLE_USER'
                ]

            ],
            'associateParent' => [
                'fullName' => "Connor Vaughan",
                'email' => 'admin@plumtreesystems.com',
                'roles' => [
                    '0' => 'ROLE_ADMIN',
                    '1' => 'ROLE_USER'
                ]
            ],
        ];

        $I->canSeeResponseContainsJson($response);

        $I->canSeeInRepository(Associate::class, [
            'email' => 'SarahCunningham@dayrep.com',
        ]);
    }
}
