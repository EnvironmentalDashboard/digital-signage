<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PresentationCarouselMapRepository")
 */
class PresentationCarouselMap
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Template", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $template;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Carousel", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $carousel;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $twig_key;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTemplate(): ?Template
    {
        return $this->template;
    }

    public function setTemplate(Template $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getCarousel(): ?Carousel
    {
        return $this->carousel;
    }

    public function setCarousel(Carousel $carousel): self
    {
        $this->carousel = $carousel;

        return $this;
    }

    public function getTwigKey(): ?string
    {
        return $this->twig_key;
    }

    public function setTwigKey(string $twig_key): self
    {
        $this->twig_key = $twig_key;

        return $this;
    }
}
