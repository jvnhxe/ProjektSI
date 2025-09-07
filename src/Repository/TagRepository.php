<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Tag>
 *
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * QueryBuilder pod listę / paginator – zwraca wszystkie tagi posortowane alfabetycznie.
     */
    public function queryAll(): QueryBuilder
    {
        return $this->createQueryBuilder('t')
            // jeśli chcesz, możesz użyć partial:
            // ->select('partial t.{id, name}')
            ->orderBy('t.name', 'ASC');
    }

    /**
     * Zwraca Tag po dokładnej nazwie (case-sensitive/case-insensitive zależnie od kolacji bazy).
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
     * Proste wyszukiwanie po prefiksie/fragmencie nazwy (do autouzupełniania).
     */
    public function searchByTerm(string $term, int $limit = 10): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.name LIKE :q')
            ->setParameter('q', '%'.$term.'%')
            ->orderBy('t.name', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Pomocniczo: znajdź wiele po nazwach.
     *
     * @param string[] $names
     * @return Tag[]
     */
    public function findByNames(array $names): array
    {
        if (empty($names)) {
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
     * Zapisz/flush w wygodny sposób.
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
     * Usuń/flush w wygodny sposób.
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
