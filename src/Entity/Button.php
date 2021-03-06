<?php

namespace App\Entity;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\RemoteController", inversedBy="buttons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $controller;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Frame")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trigger_frame;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Display")
     * @ORM\JoinColumn(nullable=false)
     */
    private $on_display;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $twig_key;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getController(): ?RemoteController
    {
        return $this->controller;
    }

    public function setController(?RemoteController $controller): self
    {
        $this->controller = $controller;

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
}
