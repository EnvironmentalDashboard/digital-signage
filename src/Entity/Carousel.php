<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Form\AbstractType;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CarouselRepository")
 */
class Carousel extends AbstractType
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
     * @ORM\OneToMany(targetEntity="App\Entity\Frame", mappedBy="carousel", orphanRemoval=true, cascade={"persist"})
     */
    private $frames;

    public function __construct()
    {
        $this->frames = new ArrayCollection();
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

    /**
     * @return Collection|Frame[]
     */
    public function getFrames(): Collection
    {
        return $this->frames;
    }

    public function addFrame(Frame $frame): self
    {
        if (!$this->frames->contains($frame)) {
            $this->frames[] = $frame;
            $frame->setCarousel($this);
        }

        return $this;
    }

    public function removeFrame(Frame $frame): self
    {
        if ($this->frames->contains($frame)) {
            $this->frames->removeElement($frame);
            // set the owning side to null (unless already changed)
            if ($frame->getCarousel() === $this) {
                $frame->setCarousel(null);
            }
        }

        return $this;
    }

    public function getLength()
    {
        return count($this->frames);
    }
}
