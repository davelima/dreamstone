<?php

namespace AppBundle\Controller\Frontend;

use AppBundle\Entity\Post;
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

        return $this->render('frontend/post/read.html.twig', [
            'section' => $section,
            'slug' => $slug,
            'post' => $post,
            'formattedPubdate' => $formattedPubdate,
            'pageTitle' => $post->getTitle(),
            'tags' => $tags
        ]);
    }
}
