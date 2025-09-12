<?php
/**
 * Post entity.
 */

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class Post.
 *
 * @psalm-suppress MissingConstructor
 */
#[ORM\Entity(repositoryClass: PostRepository::class)]
#[Vich\Uploadable]
#[ORM\Table(name: 'posts')]
class Post
{
    /**
     * Primary key.
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    /**
     * Created at.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Gedmo\Timestampable(on: 'create')]
    #[Assert\Type(\DateTimeImmutable::class)]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Updated at.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    #[Gedmo\Timestampable(on: 'update')]
    #[Assert\Type(\DateTimeImmutable::class)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Title.
     */
    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $title = null;

    /**
     * Content.
     */
    #[ORM\Column(type: 'string', length: 1000)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 1000)]
    private ?string $content = null;

    /**
     * Category.
     */
    #[ORM\ManyToOne(targetEntity: Category::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    private ?Category $category = null;

    /**
     * Author.
     */
    #[ORM\ManyToOne(targetEntity: User::class, fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Type(User::class)]
    private ?User $author = null;

    /**
     * Post date.
     */
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $postDate = null;

    #[Vich\UploadableField(mapping: 'post_images', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $imageName = null;


    /**
     * Constructor.
     */

    /**
     * Status: 'draft' or 'published'.
     */
    #[ORM\Column(length: 20, options: ['default' => 'draft'])]
    #[Assert\Choice(choices: ['draft', 'published'])]
    private string $status = 'draft';

    /**
     * Tags
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'posts')]
    #[ORM\JoinTable(name: 'posts_tags')]
    #[Assert\Count(max: 3, maxMessage: 'Możesz wybrać maksymalnie 3 tagi.')]
    private Collection $tags;

    /**
     * Post constructor: sets default values.
     */
    public function __construct()
    {
        $this->postDate = new \DateTimeImmutable();
        $this->tags = new ArrayCollection();
    }

    /**
     * Getter for Id.
     *
     * @return int|null Id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for created at.
     *
     * @return \DateTimeImmutable|null Created at
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Setter for created at.
     *
     * @param \DateTimeImmutable|null $createdAt Created at
     */
    public function setCreatedAt(?\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Getter for updated at.
     *
     * @return \DateTimeImmutable|null Updated at
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * Setter for updated at.
     *
     * @param \DateTimeImmutable|null $updatedAt Updated at
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * Sets the uploaded image file.
     *
     * @param File|null $imageFile the uploaded file instance
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if ($imageFile instanceof File) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    /**
     * Gets the uploaded image file.
     *
     * @return File|null the uploaded file instance
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * Sets the image file name.
     *
     * @param string|null $imageName the image file name
     */
    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    /**
     * Gets the image file name.
     *
     * @return string|null the image file name
     */
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    /**
     * Getter for title.
     *
     * @return string|null Title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Setter for title.
     *
     * @param string|null $title Title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * Getter for content.
     *
     * @return string|null Content
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Setter for content.
     *
     * @param string|null $content Content
     */
    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    /**
     * Getter for category.
     *
     * @return Category|null Category
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * Setter for category.
     *
     * @param Category|null $category Category
     */
    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    /**
     * Getter for author.
     *
     * @return User|null Author
     */
    public function getAuthor(): ?User
    {
        return $this->author;
    }

    /**
     * Setter for author.
     *
     * @param User|null $author Author
     */
    public function setAuthor(?User $author): void
    {
        $this->author = $author;
    }

    /**
     * Getter for post date.
     *
     * @return \DateTimeImmutable|null Post date
     */
    public function getPostDate(): ?\DateTimeImmutable
    {
        return $this->postDate;
    }

    /**
     * Setter for post date.
     *
     * @param \DateTimeImmutable|null $postDate Post date
     */
    public function setPostDate(?\DateTimeImmutable $postDate): void
    {
        $this->postDate = $postDate ?: new \DateTimeImmutable();
    }

    /**
     * Getter for status.
     *
     * @return string Status value
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Setter for status.
     *
     * @param string $status Status value ('draft' or 'published')
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /** @return Collection<int, Tag> */
    public function getTags(): Collection { return $this->tags; }

    /**
     * Adds a tag to the post if it is not already assigned.
     *
     * @param Tag $tag Tag to add.
     *
     * @return self Fluent interface.
     */
    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    /**
     * Removes a tag from the post if it is currently assigned.
     *
     * @param Tag $tag Tag to remove.
     *
     * @return self Fluent interface.
     */
    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

}
