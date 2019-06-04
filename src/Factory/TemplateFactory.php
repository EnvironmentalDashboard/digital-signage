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
    public function cloneParent($id)
    {
        if ($id <= 0) {
            throw new Exception('Cannot create from null parent');
        }
    
        $repository = $this->entityManager->getRepository(Entity\Template::class);
    
        $parent = $repository->find($id);
    
        $template = clone $parent;
    
        return $template; // if this template is saved in the db it will recieve a new ID
    }

    /**
    * @return Template entity with specified ID
    */
    public function getParent($id)
    {
        if ($id <= 0) {
            throw new Exception('Cannot create from null parent');
        }
    
        $repository = $this->entityManager->getRepository(Entity\Template::class);
    
        $parent = $repository->find($id);
    
        return $parent; // this template is already saved in db
    }
}
