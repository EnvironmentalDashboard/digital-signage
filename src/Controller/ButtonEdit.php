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
        $type = (int) $request->request->get('buttonTypeSelect');
        $display = $entityManager->getRepository(Entity\Display::class)->find($request->request->get('buttonDisplaySelect'));
        if ($display === null) {
            throw new Exception('Display ' . $request->request->get('buttonDisplaySelect') . ' does not exist');
        }

        if ($type === Entity\Button::TRIGGER_FRAME) {
            $frameId = $request->request->get('buttonFrameSelect');
            $controllerId = $request->request->get('controllerId');
            $image = $request->files->get('file');
            if ($frameId === null || $controllerId === null || $image === null) {
                throw new Exception("Missing fields: need to POST 'buttonFrameSelect', 'controllerId', 'file'; received " . print_r($request->request->all(), true));
            }
            $imageName = $this->saveImage($image);

            $frame = $entityManager->getRepository(Entity\Frame::class)->find($frameId);
            $controller = $entityManager->getRepository(Entity\RemoteController::class)->find($controllerId);
            $url = null;
        } elseif ($type === Entity\Button::PLAY) {
            $controllerId = $request->request->get('controllerId');
            if ($controllerId === null) {
                throw new Exception("Missing fields: need to POST 'controllerId'; received " . print_r($request->request->all(), true));
            }
            $imageName = 'play.svg';

            $frame = null;
            $controller = $entityManager->getRepository(Entity\RemoteController::class)->find($controllerId);
            $url = null;
        } elseif ($type === Entity\Button::TRIGGER_URL) {
            $url = $request->request->get('UrlSelect');
            $controllerId = $request->request->get('controllerId');
            $image = $request->files->get('file');
            if ($controllerId === null) {
                throw new Exception("Missing fields: need to POST 'controllerId', 'UrlSelect', 'file'; received " . print_r($request->request->all(), true));
            }
            $imageName = $this->saveImage($image);

            $frame = null;
            $controller = $entityManager->getRepository(Entity\RemoteController::class)->find($controllerId);
        } else {
            throw new Exception("Unknown button type {$type}");
        }

        $button->setOnDisplay($display);
        $button->setTriggerFrame($frame);
        $button->addController($controller);
        $button->setType($type);
        $button->setTriggerUrl($url);
        $button->setImage($imageName);

        $entityManager->persist($button);
        $entityManager->flush();

        return new JsonResponse(true);
    }

    private function saveImage($image)
    {
        if ($image->isValid()) {
            $path = '/var/www/html/public/uploads/';
            $name = $image->getClientOriginalName();
            if (file_exists($path . $name)) {
                $name = uniqid() . '.' . $image->guessClientExtension();
            }
            $image->move($path, $name);
            return $name;
        }
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
