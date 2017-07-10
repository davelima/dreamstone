<?php

namespace AppBundle\Controller\Dreamstone;

use PHPImageWorkshop\ImageWorkshop;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Administrator;
use AppBundle\Form\Type\AdministratorType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

/**
 * @Route("/dreamstone/administrators", name="administrators")
 * @author David Lima
 */
class AdministratorController extends Controller
{
    /**
     * @Route("/", name="administrators-list")
     */
    public function indexAction(Request $request)
    {
        $translator = $this->get('translator');

        $repository = $this->getDoctrine()->getRepository('AppBundle:Administrator');
        $administrators = $repository->findBy([], ['name' => 'ASC']);

        return $this->render('dreamstone/administrators/index.html.twig', [
            'pageTitle' => $translator->trans('administrators'),
            'administrators' => $administrators
        ]);
    }
    
    /**
     * @Route("/create/", name="administrators-create")
     */
    public function createAction(Request $request)
    {
        $translator = $this->get('translator');

        $administrator = new Administrator();
        $form = $this->createForm(AdministratorType::class, $administrator);
        $response = [];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->get('security.password_encoder')
                              ->encodePassword($administrator, $administrator->getPlainPassword());

            $administrator->setPassword($password);

            if ($administrator->getPhoto() && $administrator->getPhoto() instanceof UploadedFile) {
                $file = $administrator->getPhoto()->getRealPath();
                $image = ImageWorkshop::initFromPath($file);
                $image->cropToAspectRatioInPixel(500, 500, 0, 0, 'MM');
                $image->resizeInPixel(500, 500, true);

                $fileName = md5(uniqid()) . '.jpg';
                $image->save(__DIR__ . '/../../../../web/uploads/', $fileName);
                $administrator->setPhoto($fileName);
            } else {
                $administrator->setPhoto('no-photo.jpg');
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($administrator);
            $em->flush();
            
            $response['message'] = $translator->trans('administrator_created');
        }

        return $this->render('dreamstone/administrators/create.html.twig', [
            'form' => $form->createView(),
            'response' => $response
        ]);
    }

    /**
     * @Route("/edit/{id}/", name="administrators-edit")
     */
    public function editAction(Request $request, $id)
    {
        $translator = $this->get('translator');

        $em = $this->getDoctrine()->getManager();
        $administrator = $em->getRepository('AppBundle:Administrator')->find($id);
        $form = $this->createForm(AdministratorType::class, $administrator);
        $superUser = $this->isGranted('ROLE_SUPER_ADMIN', $this->getUser());
        $currentPhoto = $administrator->getPhoto();

        if ($superUser || $this->getUser()->getId() == $id) {
            $form->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false,
                'options' => [
                    'label' => 'password'
                ],
                'second_options' => [
                    'label' => 'repeat_password'
                ]
            ]);
        } else {
            $form->remove('plainPassword');
        }

        if (! $superUser) {
            $form->remove('roles');
        }

        if (! $administrator) {
            throw $this->createNotFoundException($translator->trans('administrator_not_found'));
        }

        $response = [];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($administrator->getPlainPassword()) {
                $password = $this->get('security.password_encoder')
                ->encodePassword($administrator, $administrator->getPlainPassword());
            } else {
                $password = $administrator->getPassword();
            }

            $administrator->setPassword($password);

            if ($administrator->getPhoto() && $administrator->getPhoto() instanceof UploadedFile) {
                $file = $administrator->getPhoto()->getRealPath();
                $image = ImageWorkshop::initFromPath($file);
                $image->cropToAspectRatioInPixel(500, 500, 0, 0, 'MM');
                $image->resizeInPixel(500, 500, true);

                $fileName = md5(uniqid()) . '.jpg';
                $image->save(__DIR__ . '/../../../../web/uploads/', $fileName);
                $administrator->setPhoto($fileName);
            } else {
                $administrator->setPhoto($currentPhoto);
            }

            $em->persist($administrator);
            $em->flush();

            $response['message'] = $translator->trans('administrator_updated');
        }

        return $this->render('dreamstone/administrators/create.html.twig', [
            'form' => $form->createView(),
            'response' => $response,
            'administrator' => $administrator
        ]);
    }

    /**
     * @Route("/profile/{id}/", name="administrator-profile")
     */
    public function profileAction(Request $request, $id)
    {
        $translator = $this->get('translator');

        $repository = $this->getDoctrine()->getRepository('AppBundle:Administrator');
        $administrator = $repository->find($id);

        return $this->render('dreamstone/administrators/profile.html.twig', [
            'pageTitle' => $translator->trans('administrators'),
            'administrator' => $administrator
        ]);
    }

    /**
     * @Route("/switch-status/", name="administrator-switch-status")
     * @Method({"POST"})
     */
    public function switchStatusAction(Request $request)
    {
        $translator = $this->get('translator');

        $result = [];

        $id = $request->request->get('id');
        $repository = $this->getDoctrine()->getRepository('AppBundle:Administrator');
        $administrator = $repository->find($id);

        $newStatus = $administrator->getIsActive() ? false : true;
        $administrator->setIsActive($newStatus);

        $em = $this->getDoctrine()->getManager();
        $em->persist($administrator);
        $em->flush();
        $result['message'] = $newStatus ? $translator->trans('account_enabled') : $translator->trans('account_disabled');
        $result['status'] = $newStatus;

        return $this->json($result);
    }
}
