<?php

namespace App\Controller;

use App\Entity;
use App\Factory;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\EntityManagerInterface;

class DisplayEdit extends AbstractController
{
    /**
     * display-create
     */
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $label = $request->request->get('display-label');

        // Create display
        $display = new Entity\Display;
        $display->setLabel($label);

        $entityManager->persist($display);
        $entityManager->flush();

        // Response
        return new JsonResponse([
            'display' => [
                'id' => $display->getId(),
                'label' => $display->getLabel()
            ]
        ]);
    }

    /**
     * display-delete
     */
    public function delete($id, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Entity\Display::class);

        // Delete display
        $display = $repository->findOneBy(['id' => $id]);

        $entityManager->remove($display);
        $entityManager->flush();

        return new JsonResponse(true);
    }

    /**
     * display-save-presentations
     */
    public function savePresentations(Request $request, EntityManagerInterface $entityManager,
        Factory\TemplateFactory $templateFactory, $id)
    {
		$display_repo = $entityManager->getRepository(Entity\Display::class);
		$carousel_repo = $entityManager->getRepository(Entity\Carousel::class);

        // Fetch current presentations
		// $display = $display_repo->findOneBy(['id' => $id]);
		$display = $display_repo->find($id);
        $oldPresentations = $display->getPresentations();

        // Delete current presentations
        // TODO: possibly refactor? may lead to worse table fragmentation
        foreach ($oldPresentations as $presentationToRemove) {
            $display->removePresentation($presentationToRemove);
        }

        $templates = $request->request->get('pres_template');
		$durations = $request->request->get('pres_duration');
		$frame_arrangements = $request->request->get('frame-arrangement');
		$skips = $request->request->get('skip');
		// they're all the same length ^^^
		for ($i = 0; $i < count($templates); $i++) { 
            $parentId = $templates[$i];
			$duration = $durations[$i];
			$frame_arrangement = json_decode($frame_arrangements[$i], true);
            $skip = ($skips[$i] === 'on') ? true : false;

            /**
             * @type Entity\Template
             */
            $template = $templateFactory->fromParent($parentId);
			// todo: set custom twig
            $entityManager->persist($template);

            $presentation = new Entity\Presentation();

            $presentation->setTemplate($template);
            $presentation->setDuration((int) $duration);
            $presentation->setLabel("Presentation #{$presentation->getId()} for display #{$id}");
            $presentation->setSkip($skip);
            
			$display->addPresentation($presentation);

            $entityManager->persist($presentation);
			$entityManager->persist($display);

			foreach ($frame_arrangement as $twig_key => $carousel_id) {
				$map = new Entity\CarouselPresentationMap();
				$map->setPresentation($presentation);
				// this seems inefficient- is there not a way to create map row w/o fetching carousel from db?
				$carousel = $carousel_repo->find($carousel_id);
				$map->setCarousel($carousel);
				$map->setTemplateKey($twig_key);
				$entityManager->persist($map);
			}
        }
        
        $entityManager->flush();

        return new JsonResponse(true);
    }

    private function templateTwig($id) {
        return 'test';
    }
}
