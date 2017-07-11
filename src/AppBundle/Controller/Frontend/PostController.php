<?php

namespace AppBundle\Controller\Frontend;

use AppBundle\Entity\Post;
use AppBundle\Entity\PostRead;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/post", name="posts")
 * @author David Lima
 */
class PostController extends Controller
{

    /**
     * @Route("/{section}/{slug}/", name="posts-read")
     */
    public function readAction(Request $request, $section, $slug)
    {
        $menus = $this->container->get('utils.menus');

        $postRepository = $this->getDoctrine()->getRepository('AppBundle:Post');

        $post = $postRepository->createQueryBuilder('p');

        $post->where('p.status = :status')
            ->andWhere('p.pubDate <= :pubDate')
            ->andWhere('p.slug = :slug')
            ->setParameters([
                'status' => Post::STATUS_PUBLISHED,
                'pubDate' => date('Y-m-d H:i'),
                'slug' => $slug
            ]);

        $post->setMaxResults(1);
        $post = $post->getQuery()->execute();

        if ($post) {
            $post = $post[0];
        }

        if (! $post || $post && $post->getSection()->getSlug() != $section) {
            throw $this->createNotFoundException('Publicação não encontrada');
        }

        setlocale(LC_TIME, 'pt_BR.UTF8');

        $pubDate = explode('-', $post->getPubdate()->format('Y-m-d'));
        $formattedPubdate = strftime('%d de %B de %G', mktime(0, 0, 0, $pubDate[1], $pubDate[2], $pubDate[0]));

        $tags = [];

        if ($post->getTags()) {
            foreach ($post->getTags() as $tag) {
                $tags[] = $tag->getId();
            }
        }

        $this->registerRead($post, filter_input_array(\INPUT_GET));

        return $this->render('frontend/post/read.html.twig', [
            'section' => $section,
            'slug' => $slug,
            'post' => $post,
            'formattedPubdate' => $formattedPubdate,
            'pageTitle' => $post->getTitle(),
            'tags' => $tags,
            'menus' => $menus->getSiteMenu(),
            'preview' => false
        ]);
    }

    /**
     * @Route("/preview/{section}/{slug}/", name="posts-preview")
     */
    public function previewAction(Request $request, $section, $slug)
    {
        if (! $this->getUser()) {
            throw $this->createAccessDeniedException('Access denied');
        }

        $menus = $this->container->get('utils.menus');

        $postRepository = $this->getDoctrine()->getRepository('AppBundle:Post');

        $post = $postRepository->createQueryBuilder('p');

        $post->where('p.slug = :slug')
            ->setParameters([
                'slug' => $slug
            ]);

        $post->setMaxResults(1);
        $post = $post->getQuery()->execute();

        if ($post) {
            $post = $post[0];
        }

        if (! $post || $post && $post->getSection()->getSlug() != $section) {
            throw $this->createNotFoundException('Publicação não encontrada');
        }

        setlocale(LC_TIME, 'pt_BR.UTF8');

        $pubDate = explode('-', $post->getPubdate()->format('Y-m-d'));
        $formattedPubdate = strftime('%d de %B de %G', mktime(0, 0, 0, $pubDate[1], $pubDate[2], $pubDate[0]));

        $tags = [];

        if ($post->getTags()) {
            foreach ($post->getTags() as $tag) {
                $tags[] = $tag->getId();
            }
        }

        return $this->render('frontend/post/read.html.twig', [
            'section' => $section,
            'slug' => $slug,
            'post' => $post,
            'formattedPubdate' => $formattedPubdate,
            'pageTitle' => 'PREVIEW | ' . $post->getTitle(),
            'tags' => $tags,
            'menus' => $menus->getSiteMenu(),
            'preview' => true
        ]);
    }

    /**
     * Registers a post read on database
     *
     * @param Post $post
     * @param array|null $params
     * @return PostRead
     */
    private function registerRead(Post $post, array $params = null)
    {
        $em = $this->getDoctrine()->getManager();

        $readReport = new PostRead();
        $readReport->setDateTime(new \DateTime());
        $readReport->setIp($_SERVER['REMOTE_ADDR']);
        $readReport->setUserAgent($_SERVER['HTTP_USER_AGENT']);
        $readReport->setPost($post);
        $readReport->setParams(json_encode($params));

        $em->persist($readReport);
        $em->flush();
        return $readReport;
    }
}
