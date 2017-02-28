<?php

namespace AppBundle\Controller\Dreamstone;

use AppBundle\Entity\Post;
use AppBundle\Utils\Disqus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\VarDumper\VarDumper;

class DefaultController extends Controller
{
    /**
     * @Route("/dreamstone/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        return $this->render('dreamstone/default/index.html.twig', [
            'pageTitle' => 'Dashboard',
        ]);
    }
}
