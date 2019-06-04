<?php

namespace App\Controller;

use App\Entity;
use App\Factory;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\EntityManagerInterface;

class RemoteControllerEdit extends AbstractController
{
    /**
     * controller-create
     */
    public function create(Request $request, EntityManagerInterface $entityManager, Factory\TemplateFactory $templateFactory)
    {

        $label = $request->request->get('controller-label');

        $controller = new Entity\RemoteController;
        $controller->setLabel($label);

        $template = $templateFactory->fromParent(3); // pick default template
        $controller->setTemplate($template);

        $entityManager->persist($controller);
        $entityManager->persist($template);
        $entityManager->flush();

        return new JsonResponse(true);
    }

    /**
     * controller-delete
     */
    public function delete($id, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Entity\RemoteController::class);

        $controller = $repository->findOneBy(['id' => $id]);

        $entityManager->remove($controller);
        $entityManager->flush();

        return new JsonResponse(true);
    }
}
