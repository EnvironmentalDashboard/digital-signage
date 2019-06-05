<?php

namespace App\Controller;

use App\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\EntityManagerInterface;

use \Exception;

class ButtonEdit extends AbstractController
{

    /**
     * button-create
     */
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $button = new Entity\Button;
        $button->setTwigKey('btn1'); // tmp value to be set later

        $displayId = $request->request->get('buttonDisplaySelect');
        $frameId = $request->request->get('buttonFrameSelect');
        $controllerId = $request->request->get('controllerId');
        $image = $request->files->get('file');
        if ($displayId === null || $frameId === null || $controllerId === null || $image === null) {
            throw new Exception("Missing fields: need to POST 'buttonDisplaySelect', 'buttonFrameSelect', 'controllerId', 'file'");
        }
        if ($image->isValid()) {
            $path = '/var/www/html/uploads/';
            $name = $image->getClientOriginalName();
            if (file_exists($path . $name)) {
                $name = uniqid() . '.' . $image->guessClientExtension();
            }
            $image->move($path, $name);
            $button->setImage($path . $name);
        }

        $display = $entityManager->getRepository(Entity\Display::class)->find($displayId);
        $frame = $entityManager->getRepository(Entity\Frame::class)->find($frameId);
        $controller = $entityManager->getRepository(Entity\RemoteController::class)->find($controllerId);

        $button->setOnDisplay($display);
        $button->setTriggerFrame($frame);
        $button->setController($controller);

        $entityManager->persist($button);
        $entityManager->flush();

        return new JsonResponse(true);
    }

}
