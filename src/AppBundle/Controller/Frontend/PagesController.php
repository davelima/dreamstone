<?php

namespace AppBundle\Controller\Frontend;

use Doctrine\Common\Collections\Criteria;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @Route("/page", name="pages")
 */
class PagesController extends Controller
{

    /**
     * @Route("/{slug}/", name="pages-home")
     */
    public function indexAction(Request $request, $slug)
    {
        $pageRepository = $this->getDoctrine()->getRepository('AppBundle:Page');
        $page = $pageRepository->findOneBy([
            'slug' => $slug,
            'status' => true
        ]);

        if (! $page) {
            echo '404';
            exit();
        }

        return $this->render('frontend/pages/index.html.twig', [
            'page' => $page,
            'pageTitle' => $page->getTitle()
        ]);
    }
}
