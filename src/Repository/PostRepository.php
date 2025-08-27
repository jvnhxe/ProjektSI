<?php
/**
 * Post repository.
 */

namespace App\Repository;

use App\Dto\PostListFiltersDto;
use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class PostRepository.
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @extends ServiceEntityRepository<Post>
 */
class PostRepository extends ServiceEntityRepository
{
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Manager registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * Query all records.
     *
     * @param PostListFiltersDto $filters Filters
     *
     * @return QueryBuilder Query builder
     */
    public function queryAll(PostListFiltersDto $filters): QueryBuilder
    {
        $queryBuilder = $this->getOrCreateQueryBuilder()
            ->select(
                'partial post.{id, createdAt, updatedAt, title, postDate, status}',
                'partial category.{id, title}'
            )
            ->join('post.category', 'category')
            ->orderBy('post.updatedAt', 'DESC');

        $queryBuilder->andWhere('post.status = :status')->setParameter(':status', 'published');

        return $this->applyFiltersToList($queryBuilder, $filters);
    }

    /**
     * Query posts for given author. Optionally filter by status ('draft'/'published').
     *
     * @param User                    $author
     * @param PostListFiltersDto      $filters
     * @param string|null             $status
     *
     * @return QueryBuilder
     */
    public function queryAllByAuthor(User $author, PostListFiltersDto $filters, ?string $status = null): QueryBuilder
    {
        $qb = $this->getOrCreateQueryBuilder()
            ->select(
                'partial post.{id, createdAt, updatedAt, title, postDate, status}',
                'partial category.{id, title}'
            )
            ->join('post.category', 'category')
            ->andWhere('post.author = :author')
            ->setParameter(':author', $author)
            ->orderBy('post.updatedAt', 'DESC');

        if (null !== $status && in_array($status, ['draft','published'], true)) {
            $qb->andWhere('post.status = :status')->setParameter(':status', $status);
        }

        return $this->applyFiltersToList($qb, $filters);
    }

    /**
     * Count posts by category.
     *
     * @param Category $category Category
     *
     * @return int Number of posts in category
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countByCategory(Category $category): int
    {
        $qb = $this->getOrCreateQueryBuilder();

        return $qb->select($qb->expr()->countDistinct('post.id'))
            ->where('post.category = :category')
            ->setParameter(':category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Save entity.
     *
     * @param Post $post Post entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save(Post $post): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->persist($post);
        $this->_em->flush();
    }

    /**
     * Delete entity.
     *
     * @param Post $post Post entity
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Post $post): void
    {
        assert($this->_em instanceof EntityManager);
        $this->_em->remove($post);
        $this->_em->flush();
    }

    /**
     * Query posts by author.
     *
     * @param UserInterface      $user    User entity
     * @param PostListFiltersDto $filters Filters
     *
     * @return QueryBuilder Query builder
     */
    public function queryByAuthor(UserInterface $user, PostListFiltersDto $filters): QueryBuilder
    {
        $queryBuilder = $this->queryAll($filters);

        $queryBuilder->andWhere('post.author = :author')
            ->setParameter('author', $user);

        return $queryBuilder;
    }

    /**
     * Query actual posts.
     *
     * @param PostListFiltersDto $filters Filters
     *
     * @return QueryBuilder Query builder
     */
    public function queryActualPosts(PostListFiltersDto $filters): QueryBuilder
    {
        $queryBuilder = $this->getOrCreateQueryBuilder()
            ->select(
                'partial post.{id, createdAt, updatedAt, title, postDate, status}',
                'partial category.{id, title}'
            )
            ->join('post.category', 'category')
            ->where('post.postDate >= :start_date')
            ->andWhere('post.postDate <= :end_date')
            ->setParameter('start_date', new \DateTime('-7 days'))
            ->setParameter('end_date', new \DateTime())
            ->orderBy('post.postDate', 'ASC');

        $queryBuilder->andWhere('post.status = :status')->setParameter(':status', 'published');

        return $this->applyFiltersToList($queryBuilder, $filters);
    }

    /**
     * Query future posts.
     *
     * @param PostListFiltersDto $filters Filters
     *
     * @return QueryBuilder Query builder
     */
    public function queryFuturePosts(PostListFiltersDto $filters): QueryBuilder
    {
        $queryBuilder = $this->getOrCreateQueryBuilder()
            ->select(
                'partial post.{id, createdAt, updatedAt, title, postDate, status}',
                'partial category.{id, title}'
            )
            ->join('post.category', 'category')
            ->where('post.postDate > :current_date')
            ->setParameter('current_date', new \DateTime())
            ->orderBy('post.postDate', 'ASC');

        $queryBuilder->andWhere('post.status = :status')->setParameter(':status', 'published');

        return $this->applyFiltersToList($queryBuilder, $filters);
    }

    /**
     * Finds posts by their title.
     *
     * @param string $searchTerm the term to search for in post titles
     *
     * @return array the list of posts matching the search term
     */
    public function searchByTitle(string $searchTerm): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.title LIKE :searchTerm')
            ->setParameter('searchTerm', '%'.$searchTerm.'%')
            ->andWhere('p.status = :status')
            ->setParameter('status', 'published')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Get or create new query builder.
     *
     * @param QueryBuilder|null $queryBuilder Query builder
     *
     * @return QueryBuilder Query builder
     */
    private function getOrCreateQueryBuilder(?QueryBuilder $queryBuilder = null): QueryBuilder
    {
        return $queryBuilder ?? $this->createQueryBuilder('post');
    }

    /**
     * Apply filters to paginated list.
     *
     * @param QueryBuilder       $queryBuilder Query builder
     * @param PostListFiltersDto $filters      Filters
     *
     * @return QueryBuilder Query builder
     */
    private function applyFiltersToList(QueryBuilder $queryBuilder, PostListFiltersDto $filters): QueryBuilder
    {
        if ($filters->category instanceof Category) {
            $queryBuilder->andWhere('category = :category')
                ->setParameter('category', $filters->category);
        }

        return $queryBuilder;
    }
}
