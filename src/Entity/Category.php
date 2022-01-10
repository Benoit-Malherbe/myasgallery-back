<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @Vich\Uploadable
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"api_artwork_browse", "api_event_browse", "api_artists_browse", "api_category_browse"})
     */
    private $id;

    /**
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string", length=255)
     * @Groups({"api_artwork_browse", "api_event_browse", "api_category_browse", "api_artists_browse"})
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"api_artwork_browse", "api_event_browse", "api_artwork_browse_by_category", "api_artists_browse", "api_category_browse"})
     */
    private $name;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="categories_img", fileNameProperty="pictureName", size="pictureSize", originalName="pictureUrl")
     */
    private $picture;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $pictureSize;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"api_artwork_browse", "api_event_browse", "api_artists_browse", "api_category_browse"})
     */
    private $pictureName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Groups({"api_artwork_browse", "api_artists_browse", "api_event_browse", "api_category_browse"})
     */
    private $pictureUrl;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToMany(targetEntity=Artwork::class, mappedBy="categories")
     * @Groups({"api_event_browse", "api_artists_browse", "api_category_browse"})
     */
    private $artworks;

    public function __construct()
    {
        $this->artworks = new ArrayCollection();

        // adding a new date for each new object, corresponding to the flush date
        $this->createdAt = new DateTimeImmutable();

    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPicture(): ?File
    {
        return $this->picture;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $picture
     */
    public function setPicture(?File $picture = null): void
    {
        $this->picture = $picture;

        if (null !== $picture) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getPictureSize(): ?int
    {
        return $this->pictureSize;
    }

    public function setPictureSize(?int $pictureSize): void
    {
        $this->pictureSize = $pictureSize;
    }

    public function getPictureName(): ?string
    {
        return $this->pictureName;
    }

    public function setPictureName(?string $pictureName): void
    {
        $this->pictureName = $pictureName;
    }

    public function getPictureUrl(): ?string
    {
        // we'll catch the picture with the entire associated url
        $path = "http://localhost:8000/img/uploads/categories/";
        return $path . $this->pictureUrl;
    }

    public function setPictureUrl(?string $pictureUrl): void
    {
        $this->pictureUrl = $pictureUrl;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection|Artwork[]
     */
    public function getArtworks(): Collection
    {
        return $this->artworks;
    }

    public function addArtwork(Artwork $artwork): self
    {
        if (!$this->artworks->contains($artwork)) {
            $this->artworks[] = $artwork;
            $artwork->addCategory($this);
        }

        return $this;
    }

    public function removeArtwork(Artwork $artwork): self
    {
        if ($this->artworks->removeElement($artwork)) {
            $artwork->removeCategory($this);
        }

        return $this;
    }
}
