<?php

namespace App\Controller;

use App\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\EntityManagerInterface;

class Button extends AbstractController
{

    /**
     * button-list-by-controller
     */
    public function listByController($id, Request $request, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Entity\Button::class);
        $entities = $repository->findBy(['controller' => $id]);
        return new JsonResponse($entities);
    }

    /**
     * button-list-all
     */
    public function listAll(Request $request, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Entity\Button::class);
        $entities = $repository->findAll();
        return $this->render('button-list.html.twig', ['buttons' => $entities]);
    }

}
