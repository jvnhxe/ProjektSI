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

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'tags')]
#[ORM\Entity(repositoryClass: TagRepository::class)]
/**
 * Tag entity used to label posts.
 */
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $name = '';

    #[ORM\ManyToMany(targetEntity: Post::class, mappedBy: 'tags')]
    private Collection $posts;

    /**
     * Initializes the posts collection.
     */
    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    /**
     * Returns the tag's display name.
     *
     * @return string Tag name.
     */
    public function __toString(): string
    {
        return (string) $this->name;
    }

    /**
     * Gets the tag identifier.
     *
     * @return int|null Tag ID.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Gets the tag name.
     *
     * @return string Tag name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the tag name.
     *
     * @param string $name Tag name.
     *
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets posts associated with this tag.
     *
     * @return Collection<int, Post> Collection of posts.
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }
}
