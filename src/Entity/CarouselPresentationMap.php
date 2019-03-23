<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CarouselPresentationMapRepository")
 */
class CarouselPresentationMap
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Presentation", inversedBy="carouselPresentationMaps")
     * @ORM\JoinColumn(nullable=false)
     */
    private $presentation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Carousel", inversedBy="carouselPresentationMaps")
     * @ORM\JoinColumn(nullable=false)
     */
    private $carousel;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $template_key;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPresentation(): ?presentation
    {
        return $this->presentation;
    }

    public function setPresentation(?presentation $presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }

    public function getCarousel(): ?carousel
    {
        return $this->carousel;
    }

    public function setCarousel(?carousel $carousel): self
    {
        $this->carousel = $carousel;

        return $this;
    }

    public function getTemplateKey(): ?string
    {
        return $this->template_key;
    }

    public function setTemplateKey(string $template_key): self
    {
        $this->template_key = $template_key;

        return $this;
    }
}
