<?php

namespace App\Factory;

use App\Entity;
use \Exception;
use Doctrine\ORM\EntityManagerInterface;


class TemplateFactory
{
  private $entityManager;

  public function __construct(EntityManagerInterface $entityManager) {
    $this->entityManager = $entityManager;
  }

  /**
   * @return New Template entity created from parent
   */
  public function fromParent($id)
  {
    if ($id <= 0) {
      throw new Exception('Cannot create from null parent');
    }

    $repository = $this->entityManager->getRepository(Entity\Template::class);

    $parent = $repository->find($id);

    $template = clone $parent; // todo: does cloning create a new row?

    return $template;
  }

}