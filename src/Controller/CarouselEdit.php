<?php

namespace App\Controller;

use App\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
        $currentFrames = $carousel->getFrames();
        $newFrames = [];

        $urls = $request->request->get('frame_url');
        $durations = $request->request->get('frame_duration');
        $ids = $request->request->get('id');

        // add/update frames from form inputs
        foreach ($urls as $key => $value) {
            $frameExists = is_numeric($ids[$key]);
            if ($frameExists) {
                $frame = $frameRepo->find($ids[$key]);
                if ($frame === null) {
                    $frameExists = false;
                    $frame = new Entity\Frame;
                }
            } else {
                $frame = new Entity\Frame;
            }
            $frame->setUrl($urls[$key])->setDuration(round($durations[$key] * 1000));
                    
            if (!$frameExists) {
                $carousel->addFrame($frame);
            }
            $entityManager->persist($frame);
            $newFrames[] = (int) $frame->getId();
        }

        // make sure we have no frames on this carousel that didn't come as form input data
        foreach ($currentFrames as $currentFrame) {
            if (!in_array($currentFrame->getId(), $newFrames)) {
                $carousel->removeFrame($currentFrame);
                $entityManager->remove($currentFrame);
            }
        }
        
        $entityManager->persist($carousel);
        
        $entityManager->flush();

        return new JsonResponse(true);
    }
}
