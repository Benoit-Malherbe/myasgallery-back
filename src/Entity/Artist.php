<?php

namespace App\Entity;

use App\Repository\ArtistRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=ArtistRepository::class)
 * @Vich\Uploadable
 */
class Artist
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     * @Groups({"api_artists_browse", "api_artwork_browse", "api_event_browse", "api_category_browse"})
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
     * 
     * @Groups({"api_artists_browse", "api_artwork_browse", "api_artwork_browse_by_category", "api_event_browse", "api_category_browse"})
     */
    private $name;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="artists_img", fileNameProperty="photoName", size="photoSize", originalName="photoUrl")
     */
    private $photo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $photoSize;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Groups({"api_artwork_browse", "api_artists_browse", "api_event_browse", "api_category_browse"})
     */
    private $photoName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Groups({"api_artwork_browse", "api_artists_browse", "api_event_browse", "api_category_browse"})
     */
    private $photoUrl;

    /**
     * @ORM\Column(type="text")
     * 
     * @Groups({"api_artists_browse", "api_artwork_browse", "api_event_browse", "api_category_browse"})
     */
    private $biography;

    /**
     * @ORM\Column(type="string", length=255)
     * 
     * @Groups({"api_artists_browse", "api_artwork_browse", "api_event_browse", "api_category_browse"})
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=512)
     * 
     * @Groups({"api_artists_browse", "api_artwork_browse", "api_event_browse", "api_category_browse"})
     */

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Artwork::class, mappedBy="artists", orphanRemoval="true")
     * 
     * @Groups({"api_artists_browse"})
     */
    private $artworks;

    /**
     * @ORM\ManyToMany(targetEntity=Event::class, mappedBy="artists")
     */
    private $events;

    public function __construct()
    {
        $this->artworks = new ArrayCollection();
        $this->events = new ArrayCollection();

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

    public function getPhoto(): ?File
    {
        return $this->photo;
    }

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $photo
     */
    public function setPhoto(?File $photo = null): void
    {
        $this->photo = $photo;

        if (null !== $photo) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getPhotoSize(): ?int
    {
        return $this->photoSize;
    }

    public function setPhotoSize(?int $photoSize): void
    {
        $this->photoSize = $photoSize;
    }

    public function getPhotoName(): ?string
    {
        return $this->photoName;
    }

    public function setPhotoName(?string $photoName): void
    {
        $this->photoName = $photoName;
    }

    public function getPhotoUrl(): ?string
    {
        // we'll catch the photo with the entire associated url
        $path ="http://localhost:8000/img/uploads/artists/";
        return $path . $this->photoUrl;
    }

    public function setPhotoUrl(?string $photoUrl): void
    {
        $this->photoUrl = $photoUrl;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(string $biography): self
    {
        $this->biography = $biography;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
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
            $artwork->setArtists($this);
        }

        return $this;
    }

    public function removeArtwork(Artwork $artwork): self
    {
        if ($this->artworks->removeElement($artwork)) {
            // set the owning side to null (unless already changed)
            if ($artwork->getArtists() === $this) {
                $artwork->setArtists(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Event[]
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
            $event->addArtist($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        if ($this->events->removeElement($event)) {
            $event->removeArtist($this);
        }

        return $this;
    }
}
