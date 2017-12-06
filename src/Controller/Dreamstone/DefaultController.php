<?php

namespace App\Controller\Dreamstone;

use App\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\VarDumper\VarDumper;

class DefaultController extends Controller
{
    /**
     * @Route("/dreamstone/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $translator = $this->get('translator');

        $postsRepository = $this->getDoctrine()->getRepository('App:Post');

        $latestPosts = $postsRepository->findActive(1, null, null);

        return $this->render('dreamstone/default/index.html.twig', [
            'pageTitle' => $translator->trans('dashboard'),
            'latestPosts' => $latestPosts
        ]);
    }
}
