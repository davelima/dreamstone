<?php
namespace AppBundle\Utils;

use Doctrine\ORM\EntityManager;
#use Symfony\Component\DependencyInjection\Container;
#use Symfony\Component\DependencyInjection\ContainerInterface;

class Menus
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getSiteMenu()
    {
        $sectionRepository = $this->em->getRepository('AppBundle:Section');
        $pageRepository = $this->em->getRepository('AppBundle:Page');
        $menu = [
            'sections' => [],
            'pages' => []
        ];

        $sections = $sectionRepository->findBy([
            'status' => true,
            'showOnMenu' => true
        ]);

        $pages = $pageRepository->findBy([
            'status' => true,
            'showOnMenu' => true
        ]);

        foreach ($sections as $section) {
            $menu['sections'][] = $section;
        }

        foreach ($pages as $page) {
            $menu['pages'][] = $page;
        }

        return $menu;
    }
}
