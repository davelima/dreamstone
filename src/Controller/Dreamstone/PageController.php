<?php

namespace App\Controller\Dreamstone;

use App\Entity\Page;
use App\Utils\Urls;
use PHPImageWorkshop\ImageWorkshop;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Type\PageType;

/**
 * @Route("/dreamstone/pages", name="pages")
 * @author David Lima
 */
class PageController extends Controller
{
    /**
     * @Route("/", name="-list")
     */
    public function indexAction(Request $request)
    {
        $translator = $this->get('translator');

        $repository = $this->getDoctrine()->getRepository('App:Page');
        $pages = $repository->findAll();

        return $this->render('dreamstone/pages/index.html.twig', [
            'pageTitle' => $translator->trans('pages'),
            'pages' => $pages
        ]);
    }

    /**
     * @Route("/create/", name="-create")
     */
    public function createAction(Request $request)
    {
        $translator = $this->get('translator');

        $page = new Page();
        $form = $this->createForm(PageType::class, $page);
        $response = [];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $page->setAuthor($this->getUser());
            $page->setCreationDate(new \DateTime());
            $page->setLastChange(new \DateTime());
            $page->setStatus(true);
            $page->setSlug(Urls::slugify($page->getTitle()));

            if ($page->getImage() && $page->getImage() instanceof UploadedFile) {
                $file = $page->getImage()->getRealPath();
                $image = ImageWorkshop::initFromPath($file);
                $image->resizeInPixel(1000, null, true);

                $fileName = md5(uniqid()) . '.jpg';
                $image->save(__DIR__ . '/../../../public/uploads/pages/', $fileName);

                $image->resizeInPixel(500, null, true);
                $image->save(__DIR__ . '/../../../public/uploads/pages/m/', $fileName);

                $image->resizeInPixel(250, null, true);
                $image->save(__DIR__ . '/../../../public/uploads/pages/p/', $fileName);

                $page->setImage($fileName);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($page);
            $em->flush();

            $response['message'] = $translator->trans('page_created');
        }

        return $this->render('dreamstone/pages/create.html.twig', [
            'form' => $form->createView(),
            'response' => $response,
            'pageTitle' => $translator->trans('create_page')
        ]);
    }

    /**
     * @Route("/edit/{id}/", name="-edit")
     */
    public function editAction(Request $request, $id)
    {
        $translator = $this->get('translator');

        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('App:Page')->find($id);
        $currentImage = $page->getImage();
        $form = $this->createForm(PageType::class, $page);


        if (! $page) {
            throw $this->createNotFoundException($translator->trans('page_not_found'));
        }

        $response = [];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $page->setSlug(Urls::slugify($page->getTitle()));
            $page->setLastChange(new \DateTime());

            if ($page->getImage() && $page->getImage() instanceof UploadedFile) {
                $file = $page->getImage()->getRealPath();
                $image = ImageWorkshop::initFromPath($file);
                $image->resizeInPixel(1000, null, true);

                $fileName = md5(uniqid()) . '.jpg';
                $image->save(__DIR__ . '/../../../public/uploads/pages/', $fileName);

                $image->resizeInPixel(500, null, true);
                $image->save(__DIR__ . '/../../../public/uploads/pages/m/', $fileName);

                $image->resizeInPixel(250, null, true);
                $image->save(__DIR__ . '/../../../public/uploads/pages/p/', $fileName);

                $page->setImage($fileName);
            } else {
                $page->setImage($currentImage);
            }


            $em->persist($page);
            $em->flush();

            $response['message'] = $translator->trans('page_updated');
        }

        return $this->render('dreamstone/pages/create.html.twig', [
            'form' => $form->createView(),
            'response' => $response,
            'page' => $page,
            'pageTitle' => $translator->trans('edit_page')
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
        $repository = $this->getDoctrine()->getRepository('App:Page');
        $page = $repository->find($id);

        $newStatus = $page->getStatus() ? false : true;
        $page->setStatus($newStatus);

        $em = $this->getDoctrine()->getManager();
        $em->persist($page);
        $em->flush();
        $result['message'] = $newStatus ? $translator->trans('page_enabled') : $translator->trans('page_disabled');
        $result['status'] = $newStatus;

        return $this->json($result);
    }
}
