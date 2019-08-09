<?php

namespace App\Controller;

use App\Entity;
use App\Service\ButtonManager;
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
    public function create(Request $request, EntityManagerInterface $entityManager, ButtonManager $buttonManager)
    {
        $type = (int) $request->request->get('buttonTypeSelect');
        $display = $entityManager->getRepository(Entity\Display::class)->find($request->request->get('buttonDisplaySelect'));
        if ($display === null) {
            throw new Exception('Display ' . $request->request->get('buttonDisplaySelect') . ' does not exist');
        }
        $frameId = $request->request->get('buttonFrameSelect');
        if ($frameId !== null) {
            $frame = $entityManager->getRepository(Entity\Frame::class)->find($frameId);
        } else {
            $frame = null;
        }
        $controllerId = $request->request->get('controllerId');
        if ($controllerId !== null) {
            $controller = $entityManager->getRepository(Entity\RemoteController::class)->find($controllerId);
        } else {
            $controller = null;
        }
        $image = $request->files->get('file');
        $url = $request->request->get('UrlSelect');

        $button = $buttonManager->create($type, $display, $frame, $controller, $image, $url);

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

        $type = (int) $request->request->get('buttonTypeSelect');
        $display = $entityManager->getRepository(Entity\Display::class)->find($request->request->get('buttonDisplaySelect'));
        if ($display === null) {
            throw new Exception('Display ' . $request->request->get('buttonDisplaySelect') . ' does not exist');
        }

        if ($type === Entity\Button::TRIGGER_FRAME) {
            $frameId = $request->request->get('buttonFrameSelect');
            if ($frameId === null) {
                throw new Exception("Missing fields: need to POST 'buttonFrameSelect'; received " . print_r($request->request->all(), true));
            }

            $frame = $entityManager->getRepository(Entity\Frame::class)->find($frameId);
            $url = null;
        } elseif ($type === Entity\Button::PLAY) {
            $frame = null;
            $url = null;
        } elseif ($type === Entity\Button::TRIGGER_URL) {
            $url = $request->request->get('UrlSelect');
            if ($url === null) {
                throw new Exception("Missing fields: need to POST 'UrlSelect'; received " . print_r($request->request->all(), true));
            }

            $frame = null;
        } else {
            throw new Exception("Unknown button type {$type}");
        }

        $button->setOnDisplay($display);
        $button->setTriggerFrame($frame);
        $button->setType($type);
        $button->setTriggerUrl($url);

        $entityManager->merge($button);
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
        $buttonImg = '/var/www/html/public/uploads/' . $button->getImage();
        if (file_exists($buttonImg)) {
            unlink($buttonImg);
        }
        $entityManager->remove($button);
        $entityManager->flush();
        return new JsonResponse(true);
    }

}
