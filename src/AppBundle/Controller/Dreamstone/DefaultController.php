<?php

namespace AppBundle\Controller\Dreamstone;

use AppBundle\Entity\Post;
use AppBundle\Utils\Disqus;
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

        return $this->render('dreamstone/default/index.html.twig', [
            'pageTitle' => $translator->trans('dashboard'),
        ]);
    }
}
