<?php

namespace App\Controller;

use App\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\EntityManagerInterface;

class Carousel extends AbstractController
{
    /**
     * carousel-table
     */
    public function list(Request $request, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Entity\Carousel::class);
        $entities = $repository->findAll();
        return $this->render('carousel-list.html.twig', ['carousels' => $entities]);
    }

    /**
     * carousel-table-all
     */
    public function table(Request $request, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Entity\Carousel::class);
        $entities = $repository->findAll();
        foreach ($entities as $carousel) {
            if (count($carousel->getFrames()) === 0) {
                $tmp = new Entity\Frame;
                $tmp->setUrl('http://example.com');
                $tmp->setDuration(0);
                $tmp->setCarousel($carousel);
                $carousel->addFrame($tmp);
            }
        }
        return $this->render('carousel-table.html.twig', ['carousels' => $entities]);
    }
}
