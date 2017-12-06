<?php

namespace App\Controller\Frontend;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/page", name="pages")
 */
class PagesController extends BaseController
{

    /**
     * @Route("/{slug}/", name="-home")
     */
    public function indexAction(Request $request, $slug)
    {
        $pageRepository = $this->getDoctrine()->getRepository('App:Page');
        $page = $pageRepository->findOneBy([
            'slug' => $slug,
            'status' => true
        ]);

        if (! $page) {
            throw $this->createNotFoundException('Page not found');
        }

        return $this->render('frontend/pages/index.html.twig', [
            'page' => $page,
            'pageTitle' => $page->getTitle(),
            'menus' => $this->menus->getSiteMenu()
        ]);
    }
}
