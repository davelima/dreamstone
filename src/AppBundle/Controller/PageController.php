<?php

namespace AppBundle\Controller\Dreamstone;

use AppBundle\Entity\Page;
use AppBundle\Utils\Urls;
use PHPImageWorkshop\ImageWorkshop;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\Type\PageType;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @Route("/dreamstone/pages", name="pages")
 * @author David Lima
 */
class PageController extends Controller
{
    /**
     * @Route("/", name="pages-list")
     */
    public function indexAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:Page');
        $pages = $repository->findAll();

        return $this->render('dreamstone/pages/index.html.twig', [
            'pageTitle' => 'Páginas',
            'pages' => $pages
        ]);
    }

    /**
     * @Route("/create/", name="pages-create")
     */
    public function createAction(Request $request)
    {
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

            if ($page->getFeaturedImage() && $page->getFeaturedImage() instanceof UploadedFile) {
                $file = $page->getFeaturedImage()->getRealPath();
                $image = ImageWorkshop::initFromPath($file);
                $image->cropToAspectRatioInPixel(1000, 1000, 0, 0, 'MM');
                $image->resizeInPixel(1000, 1000, true);

                $fileName = md5(uniqid()) . '.jpg';
                $image->save(__DIR__ . '/../../../../web/uploads/pages/', $fileName);

                $image->cropToAspectRatioInPixel(500, 500, 0, 0, 'MM');
                $image->resizeInPixel(500, 500, true);
                $image->save(__DIR__ . '/../../../../web/uploads/pages/m/', $fileName);

                $image->cropToAspectRatioInPixel(250, 250, 0, 0, 'MM');
                $image->resizeInPixel(250, 250, true);
                $image->save(__DIR__ . '/../../../../web/uploads/pages/p/', $fileName);

                $page->setFeaturedImage($fileName);
            }

            if ($page->getShareImage() && $page->getShareImage() instanceof UploadedFile) {
                $file = $page->getShareImage()->getRealPath();
                $image = ImageWorkshop::initFromPath($file);
                $image->cropToAspectRatioInPixel(1000, 500, 0, 0, 'MM');
                $image->resizeInPixel(1000, 500, true);

                $fileName = md5(uniqid()) . '.jpg';
                $image->save(__DIR__ . '/../../../../web/uploads/pages/', $fileName);
                $page->setShareImage($fileName);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($page);
            $em->flush();

            $response['message'] = 'Página cadastrada!';
        }

        return $this->render('dreamstone/pages/create.html.twig', [
            'form' => $form->createView(),
            'response' => $response
        ]);
    }

    /**
     * @Route("/edit/{id}/", name="pages-edit")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $page = $em->getRepository('AppBundle:Page')->find($id);
        $currentFeaturedImage = $page->getFeaturedImage();
        $currentShareImage = $page->getShareImage();
        $form = $this->createForm(PageType::class, $page);


        if (! $page) {
            throw $this->createNotFoundException("Página não encontrada");
        }

        $response = [];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $page->setSlug(Urls::slugify($page->getTitle()));
            $page->setLastChange(new \DateTime());

            if ($page->getFeaturedImage() && $page->getFeaturedImage() instanceof UploadedFile) {
                $file = $page->getFeaturedImage()->getRealPath();
                $image = ImageWorkshop::initFromPath($file);
                $image->cropToAspectRatioInPixel(1000, 1000, 0, 0, 'MM');
                $image->resizeInPixel(1000, 1000, true);

                $fileName = md5(uniqid()) . '.jpg';
                $image->save(__DIR__ . '/../../../../web/uploads/pages/', $fileName);

                $image->cropToAspectRatioInPixel(500, 500, 0, 0, 'MM');
                $image->resizeInPixel(500, 500, true);
                $image->save(__DIR__ . '/../../../../web/uploads/pages/m/', $fileName);

                $image->cropToAspectRatioInPixel(250, 250, 0, 0, 'MM');
                $image->resizeInPixel(250, 250, true);
                $image->save(__DIR__ . '/../../../../web/uploads/pages/p/', $fileName);

                $page->setFeaturedImage($fileName);
            } else {
                $page->setFeaturedImage($currentFeaturedImage);
            }


            if ($page->getShareImage() && $page->getShareImage() instanceof UploadedFile) {
                $file = $page->getShareImage()->getRealPath();
                $image = ImageWorkshop::initFromPath($file);
                $image->cropToAspectRatioInPixel(1000, 500, 0, 0, 'MM');
                $image->resizeInPixel(1000, 500, true);

                $fileName = md5(uniqid()) . '.jpg';
                $image->save(__DIR__ . '/../../../../web/uploads/pages/', $fileName);
                $page->setShareImage($fileName);
            } else {
                $page->setShareImage($currentShareImage);
            }

            $em->persist($page);
            $em->flush();

            $response['message'] = 'Página atualizada!';
        }

        return $this->render('dreamstone/pages/create.html.twig', [
            'form' => $form->createView(),
            'response' => $response,
            'page' => $page
        ]);
    }
    /**
     * @Route("/switch-status/", name="page-switch-status")
     * @Method({"POST"})
     */
    public function switchStatusAction(Request $request)
    {
        $result = [];

        $id = $request->request->get('id');
        $repository = $this->getDoctrine()->getRepository('AppBundle:Page');
        $page = $repository->find($id);

        $newStatus = $page->getStatus() ? false : true;
        $page->setStatus($newStatus);

        $em = $this->getDoctrine()->getManager();
        $em->persist($page);
        $em->flush();
        $result['message'] = $newStatus ? 'Página ativada!' : 'Página desativada!';
        $result['status'] = $newStatus;

        return $this->json($result);
    }
}
