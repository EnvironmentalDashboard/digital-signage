<?php

namespace App\Controller;

use App\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\EntityManagerInterface;

class CarouselEdit extends AbstractController
{
    /**
     * carousel-create
     */
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $label = $request->request->get('carousel-label');

        // Create carousel
        $carousel = new Entity\Carousel;
        $carousel->setLabel($label);

        $entityManager->persist($carousel);
        $entityManager->flush();

        // Response
        return new JsonResponse([
            'carousel' => [
                'id' => $carousel->getId(),
                'label' => $carousel->getLabel()
            ]
        ]);
    }

    /**
     * carousel-delete
     */
    public function delete($id, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Entity\Carousel::class);

        // Delete carousel
        $carousel = $repository->findOneBy(['id' => $id]);

        $entityManager->remove($carousel);
        $entityManager->flush();

        return new JsonResponse(true);
    }

    /**
     * carousel-save-frames
     */
    public function saveFrames(Request $request, EntityManagerInterface $entityManager, $id)
    {
        $carouselRepo = $entityManager->getRepository(Entity\Carousel::class);
        $frameRepo = $entityManager->getRepository(Entity\Frame::class);

        $carousel = $carouselRepo->find($id);

        $urls = $request->request->get('frame_url');
        $durations = $request->request->get('frame_duration');
        $ids = $request->request->get('id');

        foreach ($urls as $key => $value) {
            $frameExists = is_numeric($ids[$key]);
            $frame = ($frameExists) ?
                $frameRepo->find($ids[$key])
                    ->setUrl($urls[$key])
                    ->setDuration(round($durations[$key] * 1000)) :
                (new Entity\Frame())
                    ->setUrl($urls[$key])
                    ->setDuration(round($durations[$key] * 1000));
                    
            if (!$frameExists) {
                $carousel->addFrame($frame);
            }
            $entityManager->persist($frame);
        }
        
        $entityManager->persist($carousel);
        
        $entityManager->flush();

        return new JsonResponse(true);
    }
}
