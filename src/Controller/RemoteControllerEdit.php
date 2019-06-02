<?php

namespace App\Controller;

use App\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\EntityManagerInterface;

class RemoteControllerEdit extends AbstractController
{
    /**
     * remote-controller-create
     */
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Entity\RemoteController::class);

        $label = $request->request->get('controller-label');

        $controller = new Entity\RemoteController;
        $controller->setLabel($label);

        $entityManager->persist($controller);
        $entityManager->flush();

        return new JsonResponse(true);
    }
}
