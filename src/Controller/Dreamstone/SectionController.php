<?php

namespace App\Controller\Dreamstone;

use App\Entity\Post;
use App\Entity\Section;
use App\Form\Type\SectionType;
use App\Utils\Disqus;
use App\Utils\Urls;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/dreamstone/sections", name="sections")
 */
class SectionController extends Controller
{
    /**
     * @Route("/", name="-list")
     */
    public function indexAction(Request $request)
    {
        $translator = $this->get('translator');
        $repository = $this->getDoctrine()->getRepository('App:Section');
        $sections = $repository->findBy([], ['parentSection' => 'DESC', 'title' => 'ASC']);

        $sectionList = $childrens = [];

        foreach ($sections as &$item) {
            if (! $item->getParentSection()) {
                $sectionList[$item->getId()] = $item;
                $childrens[$item->getId()] = [];
            }
        }

        foreach ($sections as $section) {
            if ($section->getParentSection()) {
                $childrens[$section->getParentSection()->getId()][] = $section;
            }
        }

        return $this->render('dreamstone/sections/index.html.twig', [
            'pageTitle' => $translator->trans('sections'),
            'sections' => $sectionList,
            'childrens' => $childrens
        ]);
    }


    /**
     * @Route("/create/", name="-create")
     */
    public function createAction(Request $request)
    {
        $translator = $this->get('translator');

        $section = new Section();
        $form = $this->createForm(SectionType::class, $section);
        $response = [];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $section->setStatus(true);
            $section->setSlug(Urls::slugify($section->getTitle()));
            $section->setAuthor($this->getUser());
            $section->setCreationDate(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($section);
            $em->flush();

            $response['message'] = $translator->trans('section_created');
        }

        return $this->render('dreamstone/sections/create.html.twig', [
            'form' => $form->createView(),
            'response' => $response,
            'pageTitle' => $translator->trans('create_session')
        ]);
    }


    /**
     * @Route("/edit/{id}/", name="-edit")
     */
    public function editAction(Request $request, $id)
    {
        $translator = $this->get('translator');

        $em = $this->getDoctrine()->getManager();
        $section = $em->getRepository('App:Section')->find($id);
        $form = $this->createForm(SectionType::class, $section);


        if (! $section) {
            throw $this->createNotFoundException($translator->trans('section_not_found'));
        }

        $response = [];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $section->setSlug(Urls::slugify($section->getTitle()));

            $em->persist($section);
            $em->flush();

            $response['message'] = $translator->trans('section_updated');
        }

        return $this->render('dreamstone/sections/create.html.twig', [
            'form' => $form->createView(),
            'response' => $response,
            'section' => $section,
            'pageTitle' => $translator->trans('edit_session')
        ]);
    }

    /**
     * @Route("/switch-status/", name="-switch-status")
     * @Method({"POST"})
     */
    public function switchStatusAction(Request $request)
    {
        $translator = $this->get('translator');

        $result = [];

        $id = $request->request->get('id');
        $repository = $this->getDoctrine()->getRepository('App:Section');
        $section = $repository->find($id);

        $newStatus = $section->getStatus() ? false : true;
        $section->setStatus($newStatus);

        $em = $this->getDoctrine()->getManager();
        $em->persist($section);
        $em->flush();
        $result['message'] = $newStatus ? $translator->trans('section_enabled') : $translator->trans('section_disabled');
        $result['status'] = $newStatus;

        return $this->json($result);
    }
}
