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
        $repository = $entityManager->getRepository(Entity\Carousel::class);

        // Fetch current frames
        $carousel = $repository->findOneBy(['id' => $id]);
        $oldFrames = $carousel->getFrames();

        // Delete current frames
        foreach ($carousel->getFrames() as $frameToRemove) {
            $carousel->removeFrame($frameToRemove);
        }

        // Add new frames
        $frames = [];

        $urls = $request->request->get('frame_url');
        $durations = $request->request->get('frame_duration');

        foreach ($urls as $key => $value) {
            $carousel->addFrame(
                (new Entity\Frame())
                    ->setUrl($urls[$key])
                    ->setDuration(round($durations[$key] * 1000))
            );

            $entityManager->persist($carousel);
        }
        
        $entityManager->flush();

        return new JsonResponse(true);
    }
}
