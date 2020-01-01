<?php namespace App\Tests;

use App\Entity\Associate;
use App\Entity\File;
use App\Entity\Invitation;
use App\Entity\User;
use App\Tests\ApiTester;
use App\Tests\Helper\Api;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class TestAssociateApiCest
{
    private $token;

    public function _before(ApiTester $I)
    {
        /** @var EntityManagerInterface $em */
        $em = $I->getEntityManager();

        /** @var User $user */
        $user = $em->getRepository(User::class)->find(2);
        $this->token = $I->authenticate($user);
    }

    // Testing /api/associate home api whether it returns appropriate information object required for admin home page

    public function testAssociateHomeApi(ApiTester $I)
    {
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendGET('/api/associate');

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $response = [
            'maxLevel' => 3,
            'levels' => 4,
            'associatesInLevels' => [
                '1' => 2,
                '2' => 2,
                '3' => 3,
                '4' => 2,
            ],
            'parent' => [
                'fullName' => 'Connor Vaughan',
                'email' => 'admin@plumtreesystems.com',
                'roles' => [
                    '0' => 'ROLE_USER',
                    '1' => 'ROLE_ADMIN'
                ]
            ],
            'directAssociates' => [
                '0' => [
                    'fullName' => 'Bailey Brookes',
                    'email' => 'BaileyBrookes@dayrep.com'
                ],
                '1' => [
                    'fullName' => 'Aidan Newman',
                    'email' => 'AidanNewman@dayrep.com'
                ],
            ]
        ];

        $I->canSeeResponseContainsJson($response);
    }

    // Testing /api/associate/me api whether ir returns correct associate when given token as a param.

    public function testMe(ApiTester $I)
    {
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPOST('/api/associate/me', ['token' => $this->token]);

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $response = [
            'associate' => [
                'fullName' => 'Lucy Tomlinson',
                'email' => 'associate@example.com',
                'roles' => [
                    '0' => 'ROLE_USER'
                ]
            ]
        ];

        $I->canSeeResponseContainsJson($response);
    }

    // Testing /api/associate/profile api with various params and checking responses
    // for either invalid given params and errors or valid updated profiles.

    public function testAssociateProfile(ApiTester $I)
    {
        /** @var EntityManagerInterface $em */
        $em = $I->getEntityManager();

        $I->haveHttpHeader('accept', 'application/json');

        $I->sendPOST('/api/associate/profile', [
        ]);

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $response = [
            'updated' => false,
            'associate' => [
                'fullName' => 'Lucy Tomlinson',
                'email' => 'associate@example.com',
                'roles' => [
                    '0' => 'ROLE_USER'
                ]
            ]
        ];

        $I->canSeeResponseContainsJson($response);

        $I->sendPOST('/api/associate/profile', [
            'email' => 'justas@gmail.com',
            'oldPassword' => '12345',
            'newPassword' => [
                'first' => '12345',
                'second' => '12345'
            ],
            'associate' => [
                'fullName' => 'Justas',
                'country' => 'LT',
                'address' => 'blaha',
                'city' => 'kretinga',
                'postcode' => '12345',
                'mobilePhone' => ['country' => 'GB', 'number' => '7393334589'],
                'homePhone' => '23543',
                'agreedToEmailUpdates' => 'true',
                'agreedToTextMessageUpdates' => 'true',
                'agreedToTermsOfService' => 'true',
                'profilePicture' => null,
            ]
        ]);

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $response = [
            'formErrors' => [
                'invalidPassword' => 'Old password is not correct'
            ],
            'updated' => false,
            'associate' => [
                'fullName' => 'Lucy Tomlinson',
                'email' => 'associate@example.com',
                'roles' => [
                    '0' => 'ROLE_USER'
                ]
            ]
        ];

        $I->canSeeResponseContainsJson($response);

        $I->sendPOST('/api/associate/profile', [
            'email' => 'justasgmail.com',
            'oldPassword' => '1234',
            'newPassword' => [
                'first' => '12345',
                'second' => '12345'
            ],
            'associate' => [
                'fullName' => 'Justas',
                'country' => 'LT',
                'address' => 'blaha',
                'city' => 'kretinga',
                'postcode' => '12345',
                'mobilePhone' => ['country' => 'GB', 'number' => '7393334589'],
                'homePhone' => '23543',
                'agreedToEmailUpdates' => 'true',
                'agreedToTextMessageUpdates' => 'true',
                'agreedToTermsOfService' => 'true',
                'profilePicture' => null,
            ]
        ]);

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $response = [
            'formErrors' => [
                'invalidEmail' => 'Invalid email'
            ],
            'updated' => false,
            'associate' => [
                'fullName' => 'Lucy Tomlinson',
                'email' => 'associate@example.com',
                'roles' => [
                    '0' => 'ROLE_USER'
                ]
            ]
        ];

        $I->canSeeResponseContainsJson($response);

        $I->sendPOST('/api/associate/profile', [
            'email' => 'admin@plumtreesystems.com',
            'oldPassword' => '1234',
            'newPassword' => [
                'first' => '12345',
                'second' => '12345'
            ],
            'associate' => [
                'fullName' => 'Justas',
                'country' => 'LT',
                'address' => 'blaha',
                'city' => 'kretinga',
                'postcode' => '12345',
                'mobilePhone' => ['country' => 'GB', 'number' => '7393334589'],
                'homePhone' => '23543',
                'agreedToEmailUpdates' => 'true',
                'agreedToTextMessageUpdates' => 'true',
                'agreedToTermsOfService' => 'true',
                'profilePicture' => null,
            ]
        ]);

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $response = [
            'formErrors' => [
                'invalidEmail' => 'This email already exists'
            ],
            'updated' => false,
            'associate' => [
                'fullName' => 'Lucy Tomlinson',
                'email' => 'associate@example.com',
                'roles' => [
                    '0' => 'ROLE_USER'
                ]
            ]
        ];

        $I->canSeeResponseContainsJson($response);

        $I->sendPOST('/api/associate/profile', [
            'email' => 'justas@gmail.com',
            'oldPassword' => '1234',
            'newPassword' => [
                'first' => '12345',
                'second' => '12345'
            ],
            'associate' => [
                'fullName' => 'Justas',
                'country' => 'LT',
                'address' => 'blaha',
                'city' => 'kretinga',
                'postcode' => '12345',
                'mobilePhone' => ['country' => 'GB', 'number' => '7393334589'],
                'homePhone' => '23543',
                'agreedToEmailUpdates' => 'true',
                'agreedToTextMessageUpdates' => 'true',
                'agreedToTermsOfService' => 'true',
                'profilePicture' => null,
            ]
        ]);

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $response = [
            'formErrors' => [],
            'updated' => true,
            'associate' => [
                'fullName' => 'Justas',
                'email' => 'justas@gmail.com',
                'roles' => [
                    '0' => 'ROLE_USER'
                ],
                'homePhone' => '23543',
                'agreedToEmailUpdates' => true,
                'agreedToTextMessageUpdates' => true,
                'agreedToTermsOfService' => true,
                'country' => 'LT',
                'address' => 'blaha',
                'city' => 'kretinga',
                'postcode' => '12345',
                'mobilePhone' => '+447393334589'
            ],
        ];

        $I->canSeeResponseContainsJson($response);

        $I->canSeeInRepository(Associate::class, [
            'fullName' => 'Justas',
            'email' => 'justas@gmail.com',
            'homePhone' => '23543',
            'agreedToEmailUpdates' => true,
            'agreedToTextMessageUpdates' => true,
            'agreedToTermsOfService' => true,
            'country' => 'LT',
            'address' => 'blaha',
            'city' => 'kretinga',
            'postcode' => '12345',
        ]);
    }

    // Testing /api/associate/invite api with either correct params and checking if invitations
    // are added to database or not if wrong params are given.

    public function testAssociateInvitation(ApiTester $I)
    {
        /** @var EntityManagerInterface $em */
        $em = $I->getEntityManager();

        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $I->sendPOST('/api/associate/invite', [
        ]);

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $response = [
            'submitLabel' => 'send',
            'pagination' => [
                'numberOfPages' => 1.0,
                'currentPage' => 1
            ]
        ];

        $I->canSeeResponseContainsJson($response);

        $I->sendPOST('/api/associate/invite', [
            'email' => 'tom@gmail.com',
            'fullName' => 'Tom James'
        ]);

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $response = [
            'sent' => [
                'completed' => true,
                'address' => "tom@gmail.com"
            ]
        ];

        $I->canSeeResponseContainsJson($response);


        $I->canSeeInRepository(Invitation::class, [
            'email' => "tom@gmail.com",
            'fullName' => "Tom James"
        ]);

        $I->sendPOST('/api/associate/invite', [
            'email' => 'associate@example.com',
            'fullName' => 'Associate'
        ]);

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $response = [
            'submitLabel' => 'send',
            'formErrors' => [
                'invalidEmail' => 'Associate already exists'
            ],
            'invitations' => [
                '0' => [
                    'fullName' => 'Tom James',
                    'email' => 'tom@gmail.com',
                    'used' => false
                ]
            ],
            'pagination' => [
                'numberOfPages' => 1.0,
                'currentPage' => 1
            ]

        ];

        $I->canSeeResponseContainsJson($response);

        $sendInvitation = $em->getRepository(Invitation::class)->findBy(['email' => 'tom@gmail.com'])[0];

        $I->sendPOST('/api/associate/invite', [
            'invitationId' => $sendInvitation->getId(),
        ]);

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $response = [
            'sent' => [
                'completed' => true,
                'address' => "tom@gmail.com"
            ]
        ];

        $I->canSeeResponseContainsJson($response);
    }

    // Testing /api/associate/downline api
    // whether it returns correct information about given associate direct associates.

    public function testAssociateDownline(ApiTester $I)
    {
        /** @var EntityManagerInterface $em */
        $em = $I->getEntityManager();
        $I->haveHttpHeader('accept', 'application/json');
        $I->haveHttpHeader('content-type', 'application/json');

        $associate = $em->getRepository(Associate::class)->find(5);

        $I->sendGET('/api/associate/downline', [
            'id' => $associate->getId()
        ]);

        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);

        $response = [
            '0' => [
                'title' => 'Lewis Benson',
                'parentId' => '5'
            ],
            '1' => [
                'title' => 'Aaliyah Lees',
                'parentId' => '5'
            ]
        ];
        $I->canSeeResponseContainsJson($response);
    }
}
