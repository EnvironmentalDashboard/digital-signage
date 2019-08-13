<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PresentationRepository")
 */
class Presentation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Template")
     * @ORM\JoinColumn(nullable=false)
     */
    private $template;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Display", inversedBy="presentations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $display;

    /**
     * @ORM\Column(type="boolean")
     */
    private $skip;

    /**
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CarouselPresentationMap", mappedBy="presentation", orphanRemoval=true)
     */
    private $carouselPresentationMaps;

    /**
     * @ORM\Column(type="smallint")
     */
    private $position;

    public function __construct()
    {
        $this->carouselPresentationMaps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    public function setTemplate(?Template $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getDisplay(): ?Display
    {
        return $this->display;
    }

    public function setDisplay(?Display $display): self
    {
        $this->display = $display;

        return $this;
    }

    public function getSkip(): ?bool
    {
        return $this->skip;
    }

    public function setSkip(bool $skip): self
    {
        $this->skip = $skip;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return Collection|CarouselPresentationMap[]
     */
    public function getCarouselPresentationMaps(): Collection
    {
        return $this->carouselPresentationMaps;
    }

    public function addCarouselPresentationMap(CarouselPresentationMap $carouselPresentationMap): self
    {
        if (!$this->carouselPresentationMaps->contains($carouselPresentationMap)) {
            $this->carouselPresentationMaps[] = $carouselPresentationMap;
            $carouselPresentationMap->setPresentation($this);
        }

        return $this;
    }

    public function removeCarouselPresentationMap(CarouselPresentationMap $carouselPresentationMap): self
    {
        if ($this->carouselPresentationMaps->contains($carouselPresentationMap)) {
            $this->carouselPresentationMaps->removeElement($carouselPresentationMap);
            // set the owning side to null (unless already changed)
            if ($carouselPresentationMap->getPresentation() === $this) {
                $carouselPresentationMap->setPresentation(null);
            }
        }

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }
}
