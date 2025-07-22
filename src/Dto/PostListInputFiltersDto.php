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
     * @param int|null $categoryId Category identifier
     */
    public function __construct(public readonly ?int $categoryId = null)
    {
    }
}
