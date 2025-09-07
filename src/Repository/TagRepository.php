<?php
/*
 * This file is part of the YourProject package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for {@see Tag} entities.
 *
 * @extends ServiceEntityRepository<Tag>
 *
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    /**
     * TagRepository constructor.
     *
     * @param ManagerRegistry $registry Doctrine manager registry.
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * Builds a query for listing tags ordered alphabetically.
     *
     * @return QueryBuilder Query builder for all tags.
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            // ->select('partial t.{id, name}')
            ->orderBy('t.name', 'ASC');
    }

    /**
     * Finds a tag by its exact name.
     *
     * @param string $name Tag name to match.
     *
     * @return Tag|null The matching tag or null if none found.
     */
    public function findOneByName(string $name): ?Tag
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Searches tags by a term (case and collation depend on the database).
     *
     * @param string $term  Search term (matched with LIKE %term%).
     * @param int    $limit Maximum number of results to return.
     *
     * @return Tag[] Matching tags (limited by $limit).
     */
    public function searchByTerm(string $term, int $limit = 10): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.name LIKE :q')
            ->setParameter('q', '%' . $term . '%')
            ->orderBy('t.name', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Finds many tags by a list of names.
     *
     * @param array<int,string> $names Tag names to search for.
     *
     * @return Tag[] Matching tags.
     */
    public function findByNames(array $names): array
    {
        if ([] === $names) {
            return [];
        }

        return $this->createQueryBuilder('t')
            ->andWhere('t.name IN (:names)')
            ->setParameter('names', array_values($names))
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Persists a tag and optionally flushes the change.
     *
     * @param Tag  $tag   Tag entity to persist.
     * @param bool $flush Whether to flush immediately.
     *
     * @return void
     */
    public function save(Tag $tag, bool $flush = false): void
    {
        $em = $this->getEntityManager();
        $em->persist($tag);

        if ($flush) {
            $em->flush();
        }
    }

    /**
     * Removes a tag and optionally flushes the change.
     *
     * @param Tag  $tag   Tag entity to remove.
     * @param bool $flush Whether to flush immediately.
     *
     * @return void
     */
    public function delete(Tag $tag, bool $flush = false): void
    {
        $em = $this->getEntityManager();
        $em->remove($tag);

        if ($flush) {
            $em->flush();
        }
    }
}
