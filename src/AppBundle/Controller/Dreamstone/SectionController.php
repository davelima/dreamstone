<?php

namespace AppBundle\Controller\Dreamstone;

use AppBundle\Entity\Post;
use AppBundle\Entity\Section;
use AppBundle\Form\Type\SectionType;
use AppBundle\Utils\Disqus;
use AppBundle\Utils\Urls;
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
     * @Route("/", name="sections-list")
     */
    public function indexAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Section');
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
            'pageTitle' => 'Dashboard',
            'sections' => $sectionList,
            'childrens' => $childrens
        ]);
    }


    /**
     * @Route("/create/", name="section-create")
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
            'response' => $response
        ]);
    }


    /**
     * @Route("/edit/{id}/", name="section-edit")
     */
    public function editAction(Request $request, $id)
    {
        $translator = $this->get('translator');

        $em = $this->getDoctrine()->getManager();
        $section = $em->getRepository('AppBundle:Section')->find($id);
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

            $response['message'] = $translator->trans('section_update');
        }

        return $this->render('dreamstone/sections/create.html.twig', [
            'form' => $form->createView(),
            'response' => $response,
            'section' => $section
        ]);
    }

    /**
     * @Route("/switch-status/", name="section-switch-status")
     * @Method({"POST"})
     */
    public function switchStatusAction(Request $request)
    {
        $translator = $this->get('translator');

        $result = [];

        $id = $request->request->get('id');
        $repository = $this->getDoctrine()->getRepository('AppBundle:Section');
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
