<?php

namespace AppBundle\Menu;

use AppBundle\Entity\User;
use Knp\Menu\FactoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MenuBuilder
{
    private $factory;
    private $security;

    /**
     * MenuBuilder constructor.
     * @param FactoryInterface $factory
     * @param AuthorizationCheckerInterface $security
     */
    public function __construct(FactoryInterface $factory, AuthorizationCheckerInterface $security)
    {
        $this->factory = $factory;
        $this->security = $security;
    }

    public function dashboardSidebar(array $options)
    {
        $menu = $this->factory->createItem('root');
        // @todo dashboard
        $menu->addChild('labels.label_dashboard', array(
            'route'           => 'admin_dashboard',
//            'routeParameters' => array('country' => $this->getCountry()),
            'linkAttributes'  => ['icon' => 'paper-plane'],
        ));

        // super admin users
        if ($this->security->isGranted(User::ROLE_SUPER_ADMIN)) {
            $menu->addChild('labels.label_users', array(
                'route'          => 'app_appuser_list',
                'linkAttributes' => ['icon' => 'users']
            ));
        }

        $menu->addChild('labels.label_pipeline', array(
            'route'           => 'app_pipeline_list',
//            'routeParameters' => array('country' => $this->getCountry()),
            'linkAttributes'  => ['icon' => 'paper-plane'],
        ));

        return $menu;
    }

}