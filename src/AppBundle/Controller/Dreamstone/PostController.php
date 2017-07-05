<?php

namespace AppBundle\Controller\Dreamstone;

use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use AppBundle\Form\Type\PostType;
use AppBundle\Utils\Urls;
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
     * @Route("/", name="posts-list")
     */
    public function indexAction(Request $request)
    {
        $sectionRepository = $this->getDoctrine()->getRepository('AppBundle:Section');
        $sections = $sectionRepository->findAll();

        $statuses = Post::$statusLabels;

        $authorRepository = $this->getDoctrine()->getRepository('AppBundle:Administrator');
        $authors = $authorRepository->findAll();

        $filters = $request->query->all();

        $em = $this->getDoctrine()->getRepository('AppBundle:Post');

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
                    $sectionRepository = $this->getDoctrine()->getRepository('AppBundle:Section');

                    $section = $sectionRepository->find($filterValue);

                    if ($section) {
                        $posts->andWhere(
                            $qb->expr()->eq('p.section', ':section')
                        );
                        $qb->setParameter('section', $section);
                    }
                    break;
                case 'author':
                    $authorRepository = $this->getDoctrine()->getRepository('AppBundle:Administrator');

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
            'pageTitle' => 'Posts',
            'posts' => $posts,
            'sections' => $sections,
            'statuses' => $statuses,
            'authors' => $authors,
            'filters' => $filters
        ]);
    }

    /**
     * @Route("/create/", name="posts-create")
     */
    public function createAction(Request $request)
    {
        $this->denyAccessUnlessGranted(['ROLE_AUTHOR'], null, 'Você não tem permissão para criar novos posts');

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
            $tagRepository = $this->getDoctrine()->getRepository('AppBundle:Tag');

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
                $image->save(__DIR__ . '/../../../../web/uploads/posts/', $fileName);

                $image->resizeInPixel(500, null, true);
                $image->save(__DIR__ . '/../../../../web/uploads/posts/m/', $fileName);

                $image->resizeInPixel(250, null, true);
                $image->save(__DIR__ . '/../../../../web/uploads/posts/p/', $fileName);

                $post->setImage($fileName);
            }

            $em->persist($post);
            $em->flush();

            $response['message'] = 'Post criado!';
        }

        return $this->render('dreamstone/posts/create.html.twig', [
            'form' => $form->createView(),
            'response' => $response
        ]);
    }

    /**
     * @Route("/edit/{id}/", name="posts-edit")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('AppBundle:Post')->find($id);
        $currentImage = $post->getImage();
        $form = $this->createForm(PostType::class, $post);

        if (! $post) {
            throw $this->createNotFoundException("Post não encontrado");
        }

        if ($this->isGranted('ROLE_REVIEWER') &&
            ! $this->isGranted('ROLE_SUPER_ADMIN') &&
            $post->getStatus() != Post::STATUS_PENDING_REVISION
        )
        {
            throw $this->createAccessDeniedException('Você não pode editar esta publicação');
        }

        if ($this->isGranted('ROLE_AUTHOR') &&
            ! $this->isGranted('ROLE_SUPER_ADMIN') &&
            $post->getStatus() != Post::STATUS_DRAFT
        ) {
            throw $this->createAccessDeniedException('Você não pode editar esta publicação');
        }

        if ($post->getAuthor() != $this->getUser()) {
            $this->denyAccessUnlessGranted(['ROLE_SUPER_ADMIN', 'ROLE_REVIEWER'], null, 'Você não tem permissão para editar esse post');
        }

        $response = [];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $post->setPubDate(new \DateTime($post->getPubDate()));
            $post->setLastChange(new \DateTime());
            $post->setSlug(Urls::slugify($post->getTitle()));
            $tags = explode(',', $post->getTags());
            $tagRepository = $this->getDoctrine()->getRepository('AppBundle:Tag');
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
                $image->save(__DIR__ . '/../../../../web/uploads/posts/', $fileName);

                $image->resizeInPixel(500, null, true);
                $image->save(__DIR__ . '/../../../../web/uploads/posts/m/', $fileName);

                $image->resizeInPixel(250, null, true);
                $image->save(__DIR__ . '/../../../../web/uploads/posts/p/', $fileName);

                $post->setImage($fileName);
            } else {
                $post->setImage($currentImage);
            }

            $em->persist($post);
            $em->flush();

            $response['message'] = 'Post atualizado!';
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
            'tags' => implode(',', $tags)
        ]);
    }

}
