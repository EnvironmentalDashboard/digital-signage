<?php

namespace App\Controller;

use App\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\EntityManagerInterface;

class ButtonEdit extends AbstractController
{

    /**
     * button-create
     */
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $displayId = $request->request->get('buttonDisplaySelect');
        $frameId = $request->request->get('buttonFrameSelect');
        $controllerId = $request->request->get('controllerId');

        $display = $entityManager->getRepository(Entity\Display::class)->find($displayId);
        $frame = $entityManager->getRepository(Entity\Frame::class)->find($frameId);
        $controller = $entityManager->getRepository(Entity\RemoteController::class)->find($controllerId);

        $button = new Entity\Button;
        $button->setOnDisplay($display);
        $button->setTriggerFrame($frame);
        $button->setController($controller);
        $button->setTwigKey('btn1');

        $entityManager->persist($button);
        $entityManager->flush();

        return new JsonResponse(true);
    }

}
