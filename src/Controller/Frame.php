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
        
        $entities = $repository->findBy([
            'carousel' => $id
        ]);

        return $this->render('frame-list-edit.html.twig', ['frames' => $entities]);
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
