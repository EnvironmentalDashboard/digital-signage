<?php

namespace App\Controller;

use App\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\EntityManagerInterface;

class GoogleSlides extends AbstractController
{
    /**
     * google-slides-save
     */
    public function save(Request $request, EntityManagerInterface $entityManager, $presentationId)
    {
        $filtered = [];
        $data = json_decode($request->request->get('durations'), true);
        if (!is_array($data)) {
            throw new \Exception('Invalid durations payload');
        }
        foreach ($data as $key => $value) {
            preg_match("/duration:[\s]?(\d*)/i", $value, $matches);
            if ($matches) {
                $value = round($matches[1] * 1000);
                if ($value < 0 || $value > 1000000) {
                    $value = 7000;
                }
            } else {
                $value = 7000;
            }
            $filtered[$key] = $value;
        }

        $repository = $entityManager->getRepository(Entity\GoogleSlides::class);
        $toDelete = $repository->findOneBy(['presentationId' => $presentationId]);
        if ($toDelete) {
            $entityManager->remove($toDelete);
            $entityManager->flush(); // DELETE query isn't actually executed until flush() called
        }
        $entity = (new Entity\GoogleSlides())->setPresentationId($presentationId)->setData($filtered);
        $entityManager->persist($entity);
        $entityManager->flush();
        return new JsonResponse($filtered);
    }

    /**
     * google-slides-exists
     */
    public function exists(Request $request, EntityManagerInterface $entityManager, $presentationId)
    {
        $repository = $entityManager->getRepository(Entity\GoogleSlides::class);
        $res = ($repository->findOneBy(['presentationId' => $presentationId]) == null) ? false : true;
        return new JsonResponse($res);
    }
}
