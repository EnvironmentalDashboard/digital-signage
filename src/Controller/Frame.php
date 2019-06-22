<?php

namespace App\Controller;

use App\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\EntityManagerInterface;

class Frame extends AbstractController
{
    /**
     * frame-list-by-carousel
     */
    public function listByCarousel(Request $request, $id, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Entity\Frame::class);
        $buttonId = $request->query->get('buttonId');
        $selectedFrame = false;
        if ($buttonId !== null) {
            $button = $entityManager->getRepository(Entity\Button::class)->find($buttonId);
            if ($button !== null) {
                $selectedFrame = $button->getTriggerFrame();
            }
        }
        $frames = [];
        foreach ($repository->findBy(['carousel' => $id]) as $frame) {
            $frames[] = ['id' => $frame->getId(), 'url' => $frame->getUrl(), 'selected' => ($selectedFrame === $frame->getId()) ? true : false];
        }

        return new JsonResponse($frames);
    }

    /**
     * frame-list-by-display
     */
    public function listByDisplay(Request $request, $id, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Entity\Display::class);
        $frames = [];

        $display = $repository->find($id);
        foreach ($display->getPresentations() as $presentation) {
            $maps = $presentation->getCarouselPresentationMaps();
            foreach ($maps as $map) {
                foreach ($map->getCarousel()->getFrames() as $frame) {
                    $frames[] = ['id' => $frame->getId(), 'url' => $frame->getUrl()];
                }
            }
        }

        return new JsonResponse($frames);
    }
}
