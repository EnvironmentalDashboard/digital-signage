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
            throw new Exception("Missing fields: need to POST 'buttonDisplaySelect', 'buttonFrameSelect', 'controllerId', 'file'; received " . print_r($request->request->all(), true));
        }
        if ($image->isValid()) {
            $path = '/var/www/html/public/uploads/';
            $name = $image->getClientOriginalName();
            if (file_exists($path . $name)) {
                $name = uniqid() . '.' . $image->guessClientExtension();
            }
            $image->move($path, $name);
            $button->setImage($name);
        }

        $display = $entityManager->getRepository(Entity\Display::class)->find($displayId);
        $frame = $entityManager->getRepository(Entity\Frame::class)->find($frameId);
        $controller = $entityManager->getRepository(Entity\RemoteController::class)->find($controllerId);

        $button->setOnDisplay($display);
        $button->setTriggerFrame($frame);
        $button->addController($controller);

        $entityManager->persist($button);
        $entityManager->flush();

        return new JsonResponse(true);
    }

    /**
     * button-edit
     */
    public function edit(Request $request, EntityManagerInterface $entityManager, $id)
    {
        $repository = $entityManager->getRepository(Entity\Button::class);
        $button = $repository->find($id);
        if (!$button) {
            throw new Exception("Button #{$id} not found");
        }
        $displayId = $request->request->get('buttonDisplaySelect');
        $frameId = $request->request->get('buttonFrameSelect');
        if ($displayId === null || $frameId === null) {
            throw new Exception("Missing fields: need to POST 'buttonDisplaySelect', 'buttonFrameSelect'; received " . print_r($request->request->all(), true));
        }
        
        $display = $entityManager->getRepository(Entity\Display::class)->find($displayId);
        $frame = $entityManager->getRepository(Entity\Frame::class)->find($frameId);

        $button->setOnDisplay($display);
        $button->setTriggerFrame($frame);

        $entityManager->persist($button);
        $entityManager->flush();

        return new JsonResponse(true);

    }

    /**
     * button-delete
     */
    public function delete(Request $request, EntityManagerInterface $entityManager, $id) {
        $repository = $entityManager->getRepository(Entity\Button::class);
        $button = $repository->find($id);
        if (!$button) {
            throw new Exception("Button #{$id} not found");
        }
        $buttonImg = $button->getImage();
        if (file_exists($buttonImg)) {
            unlink($button);
        }
        $entityManager->remove($button);
        $entityManager->flush();
        return new JsonResponse(true);
    }

}
