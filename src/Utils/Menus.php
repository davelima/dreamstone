<?php
namespace App\Utils;

use Doctrine\ORM\EntityManagerInterface;

class Menus
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getSiteMenu()
    {
        $sectionRepository = $this->em->getRepository('App:Section');
        $pageRepository = $this->em->getRepository('App:Page');
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
