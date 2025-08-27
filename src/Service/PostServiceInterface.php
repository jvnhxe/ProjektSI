<?php
/**
 * Post service interface.
 */

namespace App\Service;

use App\Dto\PostListInputFiltersDto;
use App\Entity\Post;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface PostServiceInterface.
 */
interface PostServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int                     $page    Page number
     * @param User                    $author  Author
     * @param PostListInputFiltersDto $filters Filters for querying posts
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, User $author, PostListInputFiltersDto $filters): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Post $post Post entity
     */
    public function save(Post $post): void;

    /**
     * Delete entity.
     *
     * @param Post $post Post entity
     */
    public function delete(Post $post): void;

    /**
     * Get paginated list for the given author (owner view).
     *
     * @param int                     $page    Page number (>= 1)
     * @param User                    $author  Owner of posts
     * @param PostListInputFiltersDto $filters Filters from request
     * @param string|null             $status  'draft'|'published' or null for all
     */
    public function getPaginatedListForAuthor(int $page, User $author, PostListInputFiltersDto $filters, ?string $status = null): PaginationInterface;
}
