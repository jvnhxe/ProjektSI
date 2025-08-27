<?php
/**
 * Post service.
 */

namespace App\Service;

use App\Dto\PostListFiltersDto;
use App\Dto\PostListInputFiltersDto;
use App\Entity\Post;
use App\Entity\User;
use App\Repository\PostRepository;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class PostService.
 */
class PostService implements PostServiceInterface
{
    /**
     * Items per page.
     *
     * Use constants to define configuration options that rarely change instead
     * of specifying them in app/config/config.yml.
     * See https://symfony.com/doc/current/best_practices.html#configuration
     *
     * @constant int
     */
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param CategoryServiceInterface $categoryService Category service
     * @param PaginatorInterface       $paginator       Paginator
     * @param PostRepository           $postRepository  Post repository
     */
    public function __construct(private readonly CategoryServiceInterface $categoryService, private readonly PaginatorInterface $paginator, private readonly PostRepository $postRepository)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int                     $page    Page number
     * @param User                    $author  Post author
     * @param PostListInputFiltersDto $filters Filters
     *
     * @return PaginationInterface<SlidingPagination> Paginated list
     */
    public function getPaginatedList(int $page, User $author, PostListInputFiltersDto $filters): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);

        return $this->paginator->paginate(
            $this->postRepository->queryAll($filters),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Get paginated list for the given author (owner view).
     *
     * @param int                     $page    Page number (>= 1)
     * @param User                    $author  Owner of posts
     * @param PostListInputFiltersDto $filters Filters from request
     * @param string|null             $status  'draft'|'published' or null for all
     */
    public function getPaginatedListForAuthor(int $page, User $author, PostListInputFiltersDto $filters, ?string $status = null): PaginationInterface
    {
        $filters = $this->prepareFilters($filters);

        return $this->paginator->paginate(
            $this->postRepository->queryAllByAuthor($author, $filters, $status),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Post $post Post entity
     */
    public function save(Post $post): void
    {
        $this->postRepository->save($post);
    }

    /**
     * Delete entity.
     *
     * @param Post $post Post entity
     */
    public function delete(Post $post): void
    {
        $this->postRepository->delete($post);
    }

    /**
     * Prepare filters for the posts list.
     *
     * @param PostListInputFiltersDto $filters Raw filters from request
     *
     * @return PostListFiltersDto Result filters
     */
    private function prepareFilters(PostListInputFiltersDto $filters): PostListFiltersDto
    {
        return new PostListFiltersDto(
            null !== $filters->categoryId ? $this->categoryService->findOneById($filters->categoryId) : null
        );
    }
}
