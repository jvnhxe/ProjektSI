<?php
/**
 * Post list input filters DTO.
 */

namespace App\Dto;

/**
 * Class PostListInputFiltersDto.
 */
class PostListInputFiltersDto
{
    /**
     * Constructor.
     *
     * @param int|null $categoryId category identifier
     * @param int|null $tagId      tag identifier
     */
    public function __construct(public readonly ?int $categoryId = null, public readonly ?int $tagId = null)
    {
    }
}
