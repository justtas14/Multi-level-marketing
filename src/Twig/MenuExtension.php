<?php


namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MenuExtension extends AbstractExtension
{
    /**
     * @codeCoverageIgnore
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('getMenu', [$this, 'getMenu']),
        ];
    }

    public function getMenu()
    {
        return [
            'adminRoutes' => $this->getAdminRoutes(),
            'associateRoutes' => $this->getAssociateRoutes()
        ];
    }

    public function getAdminRoutes()
    {
        return [
            [
                'route' => 'admin',
                'label' => 'Home'
            ],
            [
                'route' => 'email_template_list',
                'label' => 'Email Templates'
            ],
            [
                'route' => 'end_prelaunch',
                'label' => 'End Prelaunch'
            ],
            [
                'route' => 'change_content',
                'label' => 'Change Content'
            ],
            [
                'route' => 'user_search',
                'label' => 'User Search'
            ],
            [
                'route' => 'csv',
                'label' => 'Associate csv dump'
            ]
        ];
    }

    public function getAssociateRoutes()
    {
        return [
            [
                'route' => 'associate',
                'label' => 'Associate Home'
            ],
            [
                'route' => 'team_viewer',
                'label' => 'Team Viewer'
            ],
            [
                'route' => 'associate_invite',
                'label' => 'Invite Associates'
            ],
            [
                'route' => 'associate_profile',
                'label' => 'Edit Profile'
            ]
        ];
    }
}
