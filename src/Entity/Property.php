<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PropertyRepository;
use App\Validator\BanWords;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PropertyRepository::class)]

#[UniqueEntity('title')]
#[UniqueEntity('slug')]

#[Vich\Uploadable]

class Property extends AbstractController
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 5)]
    private ?string $title = null;

    #[ORM\Column]
    #[Assert\NotBlank()]
    #[Assert\Positive()]
    private ?float $surface = null;

    #[ORM\Column]
    #[Assert\NotBlank()]
    #[Assert\Positive()]
    private ?float $price = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 10)]
    private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank()]
    private ?int $rooms = null;

    #[ORM\Column]
    #[Assert\NotBlank()]
    #[Assert\Positive()]
    private ?int $bedrooms = null;

    #[ORM\Column]
    #[Assert\NotBlank()]
    #[Assert\Positive()]
    private ?int $floor = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 7)]
    private ?string $adress = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $city = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Positive()]
    #[Assert\Length(min: 4)]
    private ?string $postal_code = null;

    #[ORM\Column]
    private ?bool $sold = null;

    #[ORM\ManyToOne(inversedBy: 'properties', cascade:['remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?PropertyType $propertyType = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: Heating::class, inversedBy: 'properties', cascade:['remove'])]
    private Collection $heaters;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 5, groups: ['Extra'])]
    #[Assert\Regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', message: 'This value is not valid slug')]
    private ?string $slug = null;

    #[Assert\NotBlank(groups: ['pictureRequired'])]
    #[Assert\Image(/* mimeTypes: ['gif', 'pdf'] */)]
    #[Vich\UploadableField(mapping: 'properties', fileNameProperty: 'picture')]
    private ?File $pictureFile = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picture = null;

    #[ORM\ManyToMany(targetEntity: Specificity::class, inversedBy: 'properties', cascade:['remove'])]
    private Collection $specificities;

    #[ORM\ManyToOne(inversedBy: 'properties', cascade:['remove'])]
    private ?User $user = null;

    public function __construct()
    {
        $this->sold = false;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();

        $this->heaters = new ArrayCollection();
        $this->specificities = new ArrayCollection();
    }

    function fileUpload(Form $form) : void {
        /**
        * var File $file
        */
        $file = $form->get('pictureFile')->getData();
        dd($file);
        dd($file->getClientOriginalName(), $file->getClientOriginalExtension());
        $filename = $this->getId() . '.' . $file->getClientOriginalExtension();
        $file->move($this->getParameter('kernel.project_dir') . '/public/images/properties', $filename);
        $this->setPicture($filename);
    }

    function getFormattedPrice () : string {
        return number_format($this->price, 2, '.', '.');
    }

    /* function getFormattedDescription () : string {
        return ;
    } */

    /* function formattedSlug () : string {
        return ;
    } */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSurface(): ?float
    {
        return $this->surface;
    }

    public function setSurface(float $surface): static
    {
        $this->surface = $surface;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): static
    {
        $this->rooms = $rooms;

        return $this;
    }

    public function getBedrooms(): ?int
    {
        return $this->bedrooms;
    }

    public function setBedrooms(int $bedrooms): static
    {
        $this->bedrooms = $bedrooms;

        return $this;
    }

    public function getFloor(): ?int
    {
        return $this->floor;
    }

    public function setFloor(int $floor): static
    {
        $this->floor = $floor;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): static
    {
        $this->adress = $adress;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(string $postal_code): static
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    public function isSold(): ?bool
    {
        return $this->sold;
    }

    public function setSold(bool $sold): static
    {
        $this->sold = $sold;

        return $this;
    }

    public function getPropertyType(): ?PropertyType
    {
        return $this->propertyType;
    }

    public function setPropertyType(?PropertyType $propertyType): static
    {
        $this->propertyType = $propertyType;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, Heating>
     */
    public function getHeaters(): Collection
    {
        return $this->heaters;
    }

    public function addHeater(Heating $heater): static
    {
        if (!$this->heaters->contains($heater)) {
            $this->heaters->add($heater);
        }

        return $this;
    }

    public function removeHeater(Heating $heater): static
    {
        $this->heaters->removeElement($heater);

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return Collection<int, Specificity>
     */
    public function getSpecificities(): Collection
    {
        return $this->specificities;
    }

    public function addSpecificity(Specificity $specificity): static
    {
        if (!$this->specificities->contains($specificity)) {
            $this->specificities->add($specificity);
        }

        return $this;
    }

    public function removeSpecificity(Specificity $specificity): static
    {
        $this->specificities->removeElement($specificity);

        return $this;
    }


    /**
     * Get the value of pictureFile
     */ 
    public function getPictureFile() : ?File
    {
        return $this->pictureFile;
    }

    /**
     * Set the value of pictureFile
     *
     * @return  self
     */ 
    public function setPictureFile(?File $pictureFile) : static
    {
        $this->pictureFile = $pictureFile;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

}
