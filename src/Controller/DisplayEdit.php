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
    public function savePresentations(
        Request $request,
        EntityManagerInterface $entityManager,
        Factory\TemplateFactory $templateFactory,
        $id
    ) {
        $displayRepo = $entityManager->getRepository(Entity\Display::class);
        $carouselRepo = $entityManager->getRepository(Entity\Carousel::class);
        $presentationRepo = $entityManager->getRepository(Entity\Presentation::class);

        $display = $displayRepo->find($id);
        $label = $request->request->get('display-label');
        $display->setLabel($label);

        $templates = $request->request->get('pres_template');
        $durations = $request->request->get('pres_duration');
        $frameArrangements = $request->request->get('frame-arrangement');
        $skips = $request->request->get('skip');
        $ids = $request->request->get('id');
		// they're all the same length ^^^
        for ($i = 0; $i < count($templates); $i++) {
            $parentId = (int) $templates[$i];
            $duration = round($durations[$i] * 1000);
            $frameArrangement = json_decode($frameArrangements[$i], true);
            $skip = ($skips[$i] === '1') ? true : false;

            $presentationExists = is_numeric($ids[$i]);
			$presentation = ($presentationExists) ? $presentationRepo->find($ids[$i]) : new Entity\Presentation();
			
			if ($parentId !== -1) { // not custom template
				$template = $templateFactory->fromParent($parentId);
				// todo: set custom twig
				$entityManager->persist($template);
				$presentation->setTemplate($template);
			}
            $presentation->setDuration($duration);
            $presentation->setLabel("Presentation for display #{$id}");
            $presentation->setSkip($skip);
            
            if (!$presentationExists) {
                $display->addPresentation($presentation);
            }

            $entityManager->persist($presentation);
			$entityManager->persist($display);
			
            foreach ($frameArrangement as $twig_key => $carousel_id) {
                $map = new Entity\CarouselPresentationMap();
                $map->setPresentation($presentation);
                // this seems inefficient- is there not a way to create map row w/o fetching carousel from db?
                $carousel = $carouselRepo->find($carousel_id);
                $map->setCarousel($carousel);
                $map->setTemplateKey($twig_key);
                $entityManager->persist($map);
            }
        }
        
        $entityManager->flush();

        return new JsonResponse(true);
    }

    private function templateTwig($id)
    {
        return 'test';
    }
}
