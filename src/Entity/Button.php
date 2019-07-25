<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ButtonRepository")
 */
class Button
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\RemoteController", inversedBy="buttons")
     */
    private $controller;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Frame")
     * @ORM\JoinColumn(nullable=true)
     */
    private $trigger_frame;

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $trigger_url;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Display")
     * @ORM\JoinColumn(nullable=false)
     */
    private $on_display;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $twig_key;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="smallint")
     */
    private $type;

    public const TRIGGER_FRAME = 1;
    public const PLAY = 2;
    public const TRIGGER_URL = 3;

    public function __construct()
    {
        $this->controller = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getController(): ?RemoteController
    {
        return $this->controller;
    }

    public function addController(RemoteController $controller): self
    {
        if (!$this->controller->contains($controller)) {
            $this->controller[] = $controller;
        }

        return $this;
    }

    public function removeController(RemoteController $controller): self
    {
        if ($this->controller->contains($controller)) {
            $this->controller->removeElement($controller);
        }

        return $this;
    }

    public function getTriggerFrame(): ?Frame
    {
        return $this->trigger_frame;
    }

    public function setTriggerFrame(?Frame $trigger_frame): self
    {
        $this->trigger_frame = $trigger_frame;

        return $this;
    }

    public function getTriggerUrl(): ?string
    {
        return $this->trigger_url;
    }

    public function setTriggerUrl(?string $trigger_url): self
    {
        $this->trigger_url = $trigger_url;

        return $this;
    }

    public function getOnDisplay(): ?Display
    {
        return $this->on_display;
    }

    public function setOnDisplay(?Display $on_display): self
    {
        $this->on_display = $on_display;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }
}
