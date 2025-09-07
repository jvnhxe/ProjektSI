<?php

/**
 * Comment repository.
 */

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Comment>
 */
class CommentRepository extends ServiceEntityRepository
{
    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager Registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    /**
     * Query all comments by post.
     *
     * @param Post $post Post entity
     *
     * @return QueryBuilder QueryBuilder
     */
    public function queryAllByPost(Post $post): QueryBuilder
    {
        return $this->createQueryBuilder('comment')
            ->select('partial comment.{id, email, content}')
            ->andWhere('comment.post = :post')
            ->setParameter('post', $post)
            ->orderBy('comment.id', 'DESC');
    }

    /**
     * Save entity.
     *
     * @param Comment $comment Post entity
     *
     * @throws ORMException
     */
    public function save(Comment $comment): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($comment);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Comment $comment Comment entity
     *
     * @throws ORMException
     */
    public function delete(Comment $comment): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($comment);
        $this->_em->flush();
    }

    /**
     * Find comments by post.
     *
     * @param Post $post Post entity
     *
     * @return QueryBuilder QueryBuilder
     */
    public function findByPost(Post $post): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->andWhere('comment.post = :post')
            ->setParameter('post', $post);
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder QueryBuilder
     *
     * @return QueryBuilder QueryBuilder
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('comment');
    }
}
