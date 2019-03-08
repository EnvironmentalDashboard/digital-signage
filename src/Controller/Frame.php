<?php

namespace App\Controller;

use App\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

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
}
