<?php
/*
 * This file is part of the YourProject package.
 *
 * (c) Your Name <your-email@example.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Dto;

use App\Entity\Category;

/**
 * Class PostListFiltersDto.
 *
 * Data Transfer Object (DTO) for holding filters for the Post list.
 */
class PostListFiltersDto
{
    /**
     * PostListFiltersDto constructor.
     *
     * @param Category|null           $category Category filter
     * @param \DateTimeInterface|null $dateFrom Date filter from
     * @param \DateTimeInterface|null $dateTo   Date filter to
     */
    public function __construct(public ?Category $category = null, private readonly ?\DateTimeInterface $dateFrom = null, private readonly ?\DateTimeInterface $dateTo = null)
    {
    }

    /**
     * Get the category filter.
     *
     * @return Category|null Category filter
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Get the date filter from.
     *
     * @return \DateTimeInterface|null Date filter from
     */
    public function getDateFrom(): ?\DateTimeInterface
    {
        return $this->dateFrom;
    }

    /**
     * Get the date filter to.
     *
     * @return \DateTimeInterface|null Date filter to
     */
    public function getDateTo(): ?\DateTimeInterface
    {
        return $this->dateTo;
    }
}
