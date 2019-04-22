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
		foreach ($request->query->get('durations') as $key => $value) {
			preg_match("/duration:[\s]?(\d*)/i", $value, $matches);
			if ($matches) {
				$value = round($matches[1] * 1000);
				if ($value < 0 || $value > 1000000) {
					$value = 3000;
				}
			} else {
				$value = 3000;
			}
			$filtered[$key] = $value;
		}
		$entity = (new Entity\GoogleSlides())->setPresentationId($presentationId)->setData($filtered);
		$entityManager->persist($entity);
        $entityManager->flush();
        return new JsonResponse(true);
    }
}
