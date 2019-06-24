<?php

namespace App\Factory;

use App\Entity;
use \Exception;
use Doctrine\ORM\EntityManagerInterface;

class TemplateFactory
{
    private $entityManager;
  
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
  
    /**
    * @return New Template entity created from parent
    */
    public function cloneParent($id): Entity\Template
    {
        $repository = $this->entityManager->getRepository(Entity\Template::class);
        $parent = $repository->find($id);
        if ($parent == null) {
            throw new Exception("No template with ID {$id}");
        }
    
        $template = clone $parent;
    
        return $template; // if this template is saved in the db it will receive a new ID
    }

    /**
    * @return Template entity with specified ID
    */
    public function getParent($id): Entity\Template
    {
        $repository = $this->entityManager->getRepository(Entity\Template::class);
        $parent = $repository->find($id);
        if ($parent == null) {
            throw new Exception("No template with ID {$id}");
        }
    
        return $parent; // this template is already saved in db
    }
}
