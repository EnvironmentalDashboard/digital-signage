<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RemoteControllerRepository")
 */
class RemoteController
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
     * @ORM\OneToMany(targetEntity="App\Entity\Button", mappedBy="controller", orphanRemoval=true)
     */
    private $buttons;

    public function __construct()
    {
        $this->buttons = new ArrayCollection();
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

    /**
     * @return Collection|Button[]
     */
    public function getButtons(): Collection
    {
        return $this->buttons;
    }

    public function addButton(Button $button): self
    {
        if (!$this->buttons->contains($button)) {
            $this->buttons[] = $button;
            $button->setController($this);
        }

        return $this;
    }

    public function removeButton(Button $button): self
    {
        if ($this->buttons->contains($button)) {
            $this->buttons->removeElement($button);
            // set the owning side to null (unless already changed)
            if ($button->getController() === $this) {
                $button->setController(null);
            }
        }

        return $this;
    }
}
