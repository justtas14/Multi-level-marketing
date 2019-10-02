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
                'label' => 'Home',
                'icon' => 'home'
            ],
            [
                'route' => 'email_template_list',
                'label' => 'Email Templates',
                'icon' => 'email'
            ],
            [
                'route' => 'end_prelaunch',
                'label' => 'End Prelaunch',
                'icon' => 'stop'
            ],
            [
                'route' => 'change_content',
                'label' => 'Change Content',
                'icon' => 'edit'

            ],
            [
                'route' => 'user_search',
                'label' => 'User Search',
                'icon' => 'search',
                'subRoute' => ['user_search_details']
            ],
            [
                'route' => 'gallery',
                'icon' => 'collections',
                'label' => 'Gallery'
            ],
            [
                'route' => 'logs',
                'icon' => 'library_books',
                'label' => 'System logs'
            ],
            [
                'route' => 'csv',
                'icon' => 'supervised_user_circle',
                'label' => 'Associate csv dump'
            ]
        ];
    }

    public function getAssociateRoutes()
    {
        return [
            [
                'route' => 'associate',
                'icon' => 'home',
                'label' => 'Associate Home'
            ],
            [
                'route' => 'team_viewer',
                'icon' => 'people',
                'label' => 'Team Viewer'
            ],
            [
                'route' => 'associate_invite',
                'icon' => 'insert_invitation',
                'label' => 'Invite Associates'
            ],
            [
                'route' => 'associate_profile',
                'icon' => 'edit',
                'label' => 'Edit Profile'
            ]
        ];
    }
}
