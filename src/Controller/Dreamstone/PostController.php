<?php

namespace App\Controller\Dreamstone;

use App\Entity\Post;
use App\Entity\Tag;
use App\Form\Type\PostType;
use App\Utils\Urls;
use Doctrine\Common\Collections\ArrayCollection;
use PHPImageWorkshop\ImageWorkshop;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/dreamstone/posts", name="posts")
 * @author David Lima
 */
class PostController extends Controller
{
    /**
     * @Route("/", name="-list")
     */
    public function indexAction(Request $request)
    {
        $translator = $this->get('translator');

        $sectionRepository = $this->getDoctrine()->getRepository('App:Section');
        $sections = $sectionRepository->findAll();

        $statuses = Post::$statusLabels;

        $authorRepository = $this->getDoctrine()->getRepository('App:Administrator');
        $authors = $authorRepository->findAll();

        $filters = $request->query->all();

        $em = $this->getDoctrine()->getRepository('App:Post');

        $qb = $em->createQueryBuilder('p');
        $posts = $qb->select()
            ->orderBy('p.pubDate', 'DESC');


        foreach ($filters as $filterName => $filterValue) {
            if ($filterValue == '') {
                continue;
            }

            switch ($filterName) {
                case 'q':
                    $posts->andWhere(
                        $qb->expr()->like('p.title', ':title')
                    );
                    $qb->setParameter('title', "%$filterValue%");
                    break;
                case 'from':
                    list($day, $month, $year) = explode('/', $filterValue);

                    $date = new \DateTime("$year-$month-$day 00:00:00");

                    $posts->andWhere(
                        $qb->expr()->gte('p.pubDate', ':pubdate')
                    );
                    $qb->setParameter('pubdate', $date);
                    break;
                case 'to':
                    list($day, $month, $year) = explode('/', $filterValue);

                    $date = new \DateTime("$year-$month-$day 00:00:00");

                    $posts->andWhere(
                        $qb->expr()->lte('p.pubDate', ':pubdate')
                    );
                    $qb->setParameter('pubdate', $date);
                    break;
                case 'section':
                    $sectionRepository = $this->getDoctrine()->getRepository('App:Section');

                    $section = $sectionRepository->find($filterValue);

                    if ($section) {
                        $posts->andWhere(
                            $qb->expr()->eq('p.section', ':section')
                        );
                        $qb->setParameter('section', $section);
                    }
                    break;
                case 'author':
                    $authorRepository = $this->getDoctrine()->getRepository('App:Administrator');

                    $author = $authorRepository->find($filterValue);

                    if ($author) {
                        $posts->andWhere(
                            $qb->expr()->eq('p.author', ':author')
                        );
                        $qb->setParameter('author', $author);
                    }
                    break;
                case 'status':
                    if (in_array($filterValue, array_keys(Post::$statusLabels))) {
                        $posts->andWhere(
                            $qb->expr()->eq('p.status', ':status')
                        );
                        $qb->setParameter('status', $filterValue);
                    }
                    break;
            }
        }

        if (
            ! isset($filters['status']) ||
            isset($filters['status']) &&
            $filters['status'] != Post::STATUS_REMOVED
        ) {
            $posts->andWhere($qb->expr()->not('p.status = ' . Post::STATUS_REMOVED));
        }

        $posts = $posts->getQuery()
            ->execute();

        return $this->render('dreamstone/posts/index.html.twig', [
            'pageTitle' => $translator->trans('posts'),
            'posts' => $posts,
            'sections' => $sections,
            'statuses' => $statuses,
            'authors' => $authors,
            'filters' => $filters
        ]);
    }

    /**
     * @Route("/create/", name="-create")
     */
    public function createAction(Request $request)
    {
        $translator = $this->get('translator');

        $this->denyAccessUnlessGranted(['ROLE_AUTHOR'], null, $translator->trans('permission_denied_create_post'));

        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $response = [];

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $post->setPubDate(new \DateTime($post->getPubDate()));
            $post->setLastChange(new \DateTime());
            $post->setCreationDate(new \DateTime());
            $post->setSlug(Urls::slugify($post->getTitle()));
            $post->setAuthor($this->getUser());

            if (
                ($this->isGranted('ROLE_AUTHOR') && ! $this->isGranted('ROLE_SUPER_ADMIN'))
                || ! $post->getAuthor()
            ) {
                $post->setAuthor($this->getUser());
            }

            $tags = explode(',', $post->getTags());
            $tags = array_filter($tags);

            $post->tags = new ArrayCollection();
            $tagRepository = $this->getDoctrine()->getRepository('App:Tag');

            foreach ($tags as $tag) {
                $tagExists = $tagRepository->find($tag);

                if ($tagExists) {
                    $post->tags->add($tagExists);
                } else {
                    $tagObj = new Tag();
                    $tagObj->setId($tag);
                    $post->addTag($tagObj);
                    $em->persist($tagObj);
                }
            }

            if ($post->getImage() && $post->getImage() instanceof UploadedFile) {
                $file = $post->getImage()->getRealPath();
                $image = ImageWorkshop::initFromPath($file);
                $image->resizeInPixel(1000, null, true);

                $fileName = md5(uniqid()) . '.jpg';
                $image->save(__DIR__ . '/../../../public/uploads/posts/', $fileName);

                $image->resizeInPixel(500, null, true);
                $image->save(__DIR__ . '/../../../public/uploads/posts/m/', $fileName);

                $image->resizeInPixel(250, null, true);
                $image->save(__DIR__ . '/../../../public/uploads/posts/p/', $fileName);

                $post->setImage($fileName);
            }

            $em->persist($post);
            $em->flush();

            $response['message'] = $translator->trans('post_created');
        }

        return $this->render('dreamstone/posts/create.html.twig', [
            'form' => $form->createView(),
            'response' => $response,
            'pageTitle' => $translator->trans('create_post')
        ]);
    }

    /**
     * @Route("/edit/{id}/", name="-edit")
     */
    public function editAction(Request $request, $id)
    {
        $translator = $this->get('translator');

        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('App:Post')->find($id);
        $currentImage = $post->getImage();
        $form = $this->createForm(PostType::class, $post);

        if (! $post) {
            throw $this->createNotFoundException($translator->trans('post_not_found'));
        }

        if ($this->isGranted('ROLE_REVIEWER') &&
            ! $this->isGranted('ROLE_SUPER_ADMIN') &&
            $post->getStatus() != Post::STATUS_PENDING_REVISION
        )
        {
            throw $this->createAccessDeniedException($translator->trans('permission_denied_edit_post'));
        }

        if ($this->isGranted('ROLE_AUTHOR') &&
            ! $this->isGranted('ROLE_SUPER_ADMIN') &&
            $post->getStatus() != Post::STATUS_DRAFT
        ) {
            throw $this->createAccessDeniedException($translator->trans('permission_denied_edit_post'));
        }

        if ($post->getAuthor() != $this->getUser()) {
            $this->denyAccessUnlessGranted(['ROLE_SUPER_ADMIN', 'ROLE_REVIEWER'], null, $translator->trans('permission_denied_edit_post'));
        }

        $response = [];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $post->setPubDate(new \DateTime($post->getPubDate()));
            $post->setLastChange(new \DateTime());
            $post->setSlug(Urls::slugify($post->getTitle()));
            $tags = explode(',', $post->getTags());
            $tagRepository = $this->getDoctrine()->getRepository('App:Tag');
            $tags = array_filter($tags);

            if (
                ($this->isGranted('ROLE_AUTHOR') && ! $this->isGranted('ROLE_SUPER_ADMIN'))
                || ! $post->getAuthor()
            ) {
                $post->setAuthor($this->getUser());
            }

            $post->tags = new ArrayCollection();

            foreach ($tags as $tag) {
                $tagExists = $tagRepository->find($tag);

                if ($tagExists) {
                    $post->tags->add($tagExists);
                } else {
                    $tagObj = new Tag();
                    $tagObj->setId($tag);
                    $post->addTag($tagObj);
                    $em->persist($tagObj);
                }
            }

            if ($post->getImage() && $post->getImage() instanceof UploadedFile) {
                $file = $post->getImage()->getRealPath();
                $image = ImageWorkshop::initFromPath($file);
                $image->resizeInPixel(1000, null, true);

                $fileName = md5(uniqid()) . '.jpg';
                $image->save(__DIR__ . '/../../../public/uploads/posts/', $fileName);

                $image->resizeInPixel(500, null, true);
                $image->save(__DIR__ . '/../../../public/uploads/posts/m/', $fileName);

                $image->resizeInPixel(250, null, true);
                $image->save(__DIR__ . '/../../../public/uploads/posts/p/', $fileName);

                $post->setImage($fileName);
            } else {
                $post->setImage($currentImage);
            }

            $em->persist($post);
            $em->flush();

            $response['message'] = $translator->trans('post_updated');
        }

        if (! $post->getImage()) {
            $post->setImage($currentImage);
        }

        $tags = [];
        if ($post->getTags()) {
            foreach ($post->getTags() as $tag) {
                $tags[] = $tag->getId();
            }
        }

        return $this->render('dreamstone/posts/create.html.twig', [
            'form' => $form->createView(),
            'response' => $response,
            'post' => $post,
            'tags' => implode(',', $tags),
            'pageTitle' => $translator->trans('edit_post')
        ]);
    }

    /**
     * @Route("/report/{id}/", name="-report")
     */
    public function reportAction($id)
    {
        $translator = $this->get('translator');

        $postRepository = $this->getDoctrine()->getRepository('App:Post');
        $post = $postRepository->find($id);

        if (! $post) {
            throw $this->createNotFoundException();
        }

        $to = $from = null;

        if (isset($_GET['from']) && $_GET['from']) {
            $from = filter_input(\INPUT_GET, 'from',\FILTER_SANITIZE_STRING);
            list($day, $month, $year) = explode('/', $from);
            $from = new \DateTime("$year-$month-$day");
        }

        if (isset($_GET['to']) && $_GET['to']) {
            $to = filter_input(\INPUT_GET, 'to',\FILTER_SANITIZE_STRING);
            list($day, $month, $year) = explode('/', $to);
            $to = new \DateTime("$year-$month-$day");
        }

        $readRepository = $this->getDoctrine()->getRepository('App:PostRead');
        $reads = $readRepository->getReport($post, $from, $to);

        $report = [];

        foreach ($reads['engagementChart'] as $read) {
            list($year, $month, $day) = explode('-', $read['timestamp']);
            $timestamp = "$day/$month/$year";
            $report[$timestamp] = $read['totalReads'];
        }

        return $this->render('dreamstone/posts/report.html.twig', [
            'post' => $post,
            'reportDates' => implode("','", array_keys($report)),
            'reportReads' => implode(",", array_values($report)),
            'totalReads' => $reads['totalReads'],
            'uniqueReads' => $reads['uniqueUsers'],
            'from' => $from,
            'to' => $to,
            'pageTitle' => $translator->trans('posts_report')
        ]);
    }
}
