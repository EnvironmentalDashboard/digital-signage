<?php

namespace App\Controller;

use App\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\EntityManagerInterface;

class Button extends AbstractController
{

    /**
     * button-list-by-controller
     */
    public function listByController($id, Request $request, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Entity\Button::class);
        $entities = $repository->findBy(['controller' => $id]);
        return new JsonResponse($entities);
    }

    /**
     * button-list-all
     */
    public function listAll(Request $request, EntityManagerInterface $entityManager)
    {
        $displays = $entityManager->getRepository(Entity\Display::class)->findAll();
        $buttons = $entityManager->getRepository(Entity\Button::class)->findAll();
        $frames = [];
        
        foreach ($entityManager->getRepository(Entity\Frame::class)->findAll() as $frame) {
            $cache = [];
            $carousel = $frame->getCarousel();
            foreach ($carousel->getCarouselPresentationMaps() as $relation) {
                $presentation = $relation->getPresentation();
                if (in_array($presentation->getId(), $cache)) {
                    continue;
                }
                $cache[] = $presentation->getId();
                $frames[$presentation->getDisplay()->getId()][] = $frame;
            }
        }

        return $this->render('button-list.html.twig', [
            'displays' => $displays,
            'buttons' => $buttons,
            'frames' => $frames
        ]);
    }

}
