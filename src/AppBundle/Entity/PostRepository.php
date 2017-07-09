<?php

namespace AppBundle\Entity;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Total active items count
     *
     * @var int
     */
    public $itemsCount;

    /**
     * Total page count for active items
     *
     * @var int
     */
    public $pageCount;

    /**
     * Item limit per page
     */
    const ITEMS_PER_PAGE = 10;

    /**
     * Return all active posts based on filtering
     *
     * @param int $page
     * @param null $section
     * @param null $tag
     * @return mixed
     */
    public function findActive($page = 1, $section = null, $tag = null)
    {
        $qb = $this->createQueryBuilder('p');

        if ($page < 1) {
            $page = 1;
        }

        $limit = self::ITEMS_PER_PAGE;

        $offset = ($page - 1) * $limit;

        $qb->where('p.status = :status')
            ->andWhere('p.pubDate <= :now')
            ->orderBy('p.pubDate', 'DESC');

        $qb->setParameter('status', Post::STATUS_PUBLISHED);
        $qb->setParameter('now', new \DateTime());

        if ($section instanceof Section) {
            $qb->andWhere('p.section = :section');
            $qb->setParameter('section', $section);
        }

        if ($tag) {
            $qb->innerJoin('p.tags', 't', 'WITH', 't.id = :tag')
                ->setParameter('tag', $tag);
        }

        $itemCounter = clone $qb;

        $itemCounter = $itemCounter->select(['COUNT(p.id) AS total'])->getQuery()->execute();

        $totalItems = $itemCounter[0]['total'];

        $this->itemsCount = $totalItems;
        $this->pageCount = (int) floor($totalItems / $limit);

        $qb->setFirstResult($offset)
            ->setMaxResults($limit);

        return $qb->getQuery()->execute();
    }
}
