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
        $repository = $entityManager->getRepository(Entity\Display::class);

        // Fetch current presentations
        $display = $repository->findOneBy(['id' => $id]);
        $oldPresentations = $display->getPresentations();

        // Delete current presentations
        // TODO: possibly refactor? may lead to worse table fragmentation
        foreach ($oldPresentations as $presentationToRemove) {
            $display->removePresentation($presentationToRemove);
        }

        // Add new presentations
        $presentations = [];

        $templates = $request->request->get('pres_template');
        $durations = $request->request->get('pres_duration');

        foreach ($templates as $key => $value) {
            $parentId = $templates[$key];
            $duration = $durations[$key];

            /**
             * @type Entity\Template
             */
            $template = $templateFactory->fromParent($parentId);

            $entityManager->persist($template);

            $presentation = new Entity\Presentation();

            $presentation->setTemplate($template);
            $presentation->setDuration((int) $duration);
            $presentation->setLabel('should we even have labels?');
            $presentation->setSkip(false);
            
            $display->addPresentation($presentation);

            $entityManager->persist($presentation);
            $entityManager->persist($display);
        }
        
        $entityManager->flush();

        return new JsonResponse(true);
    }

    private function templateTwig($id) {
        return 'test';
    }
}