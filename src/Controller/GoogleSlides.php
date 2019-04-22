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
		$entity = (new Entity\GoogleSlides())->setPresentationId($presentationId)->setData($request->query->get('durations'));
		$entityManager->persist($entity);
        $entityManager->flush();
        return new JsonResponse(true);
    }
}
