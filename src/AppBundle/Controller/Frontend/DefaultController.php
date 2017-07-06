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

    /**
     * @Route("/section/{slug}/", name="section-front")
     */
    public function sectionAction(Request $request, $slug)
    {
        $sectionRepository = $this->getDoctrine()->getRepository('AppBundle:Section');
        $section = $sectionRepository->findOneBy([
            'slug' => $slug
        ]);

        if (! $section) {
            throw $this->createNotFoundException('Section not found');
        }

        $page = $request->get('page', 1);
        $repository = $this->getDoctrine()->getRepository('AppBundle:Post');
        $posts = $repository->findActive(10, $page, $section);

        $menus = $this->container->get('utils.menus');

        return $this->render('frontend/default/index.html.twig', [
            'posts' => $posts,
            'menus' => $menus->getSiteMenu(),
            'pageTitle' => $section->getTitle()
        ]);
    }

    /**
     * @Route("/tag/{id}/", name="tag-front")
     */
    public function tagAction(Request $request, $id)
    {
        $page = $request->get('page', 1);
        $repository = $this->getDoctrine()->getRepository('AppBundle:Post');
        $posts = $repository->findActive(10, $page, null, $id);

        $menus = $this->container->get('utils.menus');

        return $this->render('frontend/default/index.html.twig', [
            'posts' => $posts,
            'menus' => $menus->getSiteMenu(),
            'pageTitle' => $section->getTitle()
        ]);
    }
}
