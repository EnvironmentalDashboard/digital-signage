<?php

namespace App\Controller;

use App\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\EntityManagerInterface;

class Carousel extends AbstractController
{
    /**
     * @Route("/carousel/all", name="carousel_list_all", methods={"GET"})
     */
    public function listAll(Request $request, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Entity\Carousel::class);
        
        $entities = $repository->findAll();

        return $this->render('carousel-list.html.twig', ['carousels' => $entities]);
    }
}
