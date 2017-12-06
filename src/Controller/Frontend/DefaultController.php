<?php

namespace App\Controller\Frontend;

use App\Entity\PostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends BaseController
{

    private $pagination;

    /**
     * @Route("/", name="home-front")
     */
    public function indexAction(Request $request)
    {
        $page = $request->get('page', 1);
        $repository = $this->getDoctrine()->getRepository('App:Post');
        $posts = $repository->findActive($page);

        $this->fillPagination($repository, $page);

        return $this->render('frontend/default/index.html.twig', [
            'posts' => $posts,
            'menus' => $this->menus->getSiteMenu(),
            'pagination' => $this->pagination
        ]);
    }

    /**
     * @Route("/section/{slug}/", name="section-front")
     */
    public function sectionAction(Request $request, $slug)
    {
        $sectionRepository = $this->getDoctrine()->getRepository('App:Section');
        $section = $sectionRepository->findOneBy([
            'slug' => $slug
        ]);

        if (! $section) {
            throw $this->createNotFoundException('Section not found');
        }

        $page = $request->get('page', 1);
        $repository = $this->getDoctrine()->getRepository('App:Post');
        $posts = $repository->findActive($page, $section);

        $this->fillPagination($repository, $page);

        return $this->render('frontend/default/index.html.twig', [
            'posts' => $posts,
            'menus' => $this->menus->getSiteMenu(),
            'pageTitle' => $section->getTitle(),
            'pagination' => $this->pagination
        ]);
    }

    /**
     * @Route("/tag/{id}/", name="tag-front")
     */
    public function tagAction(Request $request, $id)
    {
        $tagRepository = $this->getDoctrine()->getRepository('App:Tag');

        $tag = $tagRepository->findOneBy([
            'id' => $id
        ]);

        if (! $tag) {
            throw $this->createNotFoundException('Tag not found');
        }

        $page = $request->get('page', 1);
        $repository = $this->getDoctrine()->getRepository('App:Post');
        $posts = $repository->findActive($page, null, $id);

        $this->fillPagination($repository, $page);

        return $this->render('frontend/default/index.html.twig', [
            'posts' => $posts,
            'menus' => $this->menus->getSiteMenu(),
            'pageTitle' => $tag->getId(),
            'pagination' => $this->pagination
        ]);
    }

    /**
     * Fills data for frontend pagination
     *
     * @param PostRepository $repository
     * @param $currentPage
     */
    private function fillPagination(PostRepository $repository, $currentPage)
    {
        $pagination = [
            'next' => null,
            'prev' => null,
            'totalPages' => $repository->pageCount,
            'currentPage' => $currentPage
        ];

        if (($currentPage + 1) <= $pagination['totalPages']) {
            $pagination['next'] = $currentPage + 1;
        }

        if (($currentPage - 1) > 0) {
            $pagination['prev'] = $currentPage - 1;
        }

        $this->pagination = $pagination;
    }
}
