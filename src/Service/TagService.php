<?php
/*
 * This file is part of the YourProject package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * @license MIT
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Default implementation of TagServiceInterface.
 */
class TagService implements TagServiceInterface
{
    /**
     * @param TagRepository $tagRepository
     * @param EntityManagerInterface $em
     */
    public function __construct(private readonly TagRepository $tagRepository, private readonly EntityManagerInterface $em)
    {
    }

    /** @inheritDoc */
    public function getAllOrdered(): array
    {
        return $this->tagRepository->createQueryBuilder('t')
            ->orderBy('t.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /** @inheritDoc */
    public function findOneById(int $id): ?Tag
    {
        return $this->tagRepository->find($id);
    }

    /** @inheritDoc */
    public function save(Tag $tag): void
    {
        $this->em->persist($tag);
        $this->em->flush();
    }

    /** @inheritDoc */
    public function delete(Tag $tag): void
    {
        $this->em->remove($tag);
        $this->em->flush();
    }
}
