<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TemplateRepository")
 */
class Template
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
     * @ORM\Column(type="text")
     */
    private $twig;

    /**
     * @ORM\Column(type="object", nullable=true)
     */
    private $style;

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

    public function getTwig(): ?string
    {
        return $this->twig;
    }

    public function setTwig(string $twig): self
    {
        $this->twig = $twig;

        return $this;
    }

    public function getStyle()
    {
        return $this->style;
    }

    public function setStyle($style): self
    {
        $this->style = $style;

        return $this;
    }

}
