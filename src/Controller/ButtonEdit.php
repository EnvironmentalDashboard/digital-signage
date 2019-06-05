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
        $image = $request->files->get('file');
        $path = '/var/www/html/uploads/';
        $name = $image->getClientOriginalName();
        if ($image->isValid()) {
            if (file_exists($path . $name)) {
                $name = uniqid() . '.' . $image->guessClientExtension();
            }
            $image->move($path, $name);
        }

        $display = $entityManager->getRepository(Entity\Display::class)->find($displayId);
        $frame = $entityManager->getRepository(Entity\Frame::class)->find($frameId);
        $controller = $entityManager->getRepository(Entity\RemoteController::class)->find($controllerId);

        $button = new Entity\Button;
        $button->setOnDisplay($display);
        $button->setTriggerFrame($frame);
        $button->setController($controller);
        $button->setTwigKey('btn1');
        $button->setImage($path . $name);

        $entityManager->persist($button);
        $entityManager->flush();

        return new JsonResponse(true);
    }

}
