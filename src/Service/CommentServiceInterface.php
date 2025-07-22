<?php

/**
 * Post service interface.
 */

namespace App\Service;

use App\Dto\PostListInputFiltersDto;
use App\Entity\Comment;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface CommentServiceInterface.
 */
interface CommentServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int                     $page    Page number
     * @param PostListInputFiltersDto $filters Filters for querying comments
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, PostListInputFiltersDto $filters): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Comment $comment comment entity
     */
    public function save(Comment $comment): void;

    /**
     * Delete entity.
     *
     * @param Comment $comment comment entity
     */
    public function delete(Comment $comment): void;
}
