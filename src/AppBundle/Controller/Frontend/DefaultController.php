<?php

namespace AppBundle\Controller\Frontend;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\VarDumper\VarDumper;

class DefaultController extends Controller
{

    /**
     * @Route("/", name="home-front")
     */
    public function indexAction(Request $request)
    {
        $page = $request->get('page', 1);
        $repository = $this->getDoctrine()->getRepository('AppBundle:Post');
        $posts = $repository->findActive(10, $page);

        $menus = $this->container->get('utils.menus');

        return $this->render('frontend/default/index.html.twig', [
            'posts' => $posts,
            'menus' => $menus->getSiteMenu()
        ]);
    }
}
