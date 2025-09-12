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

/**
 * Interface for tag management use cases.
 */
interface TagServiceInterface
{
    /**
     * Returns all tags ordered by name (ASC).
     *
     * @return Tag[]
     */
    public function getAllOrdered(): array;

    /**
     * Finds a tag by its id.
     *
     * @param int $id Tag identifier
     *
     * @return Tag|null
     */
    public function findOneById(int $id): ?Tag;

    /**
     * Persists a tag.
     *
     * @param Tag $tag Tag entity to save
     *
     * @return void
     */
    public function save(Tag $tag): void;

    /**
     * Deletes a tag.
     *
     * @param Tag $tag Tag entity to delete
     *
     * @return void
     */
    public function delete(Tag $tag): void;
}
