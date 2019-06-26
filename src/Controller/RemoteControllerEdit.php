<?php

namespace App\Controller;

use App\Entity;
use App\Factory;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\EntityManagerInterface;

class RemoteControllerEdit extends AbstractController
{
    /**
     * controller-create
     */
    public function create(Request $request, EntityManagerInterface $entityManager, Factory\TemplateFactory $templateFactory)
    {
        $label = $request->request->get('controller-label');

        $controller = new Entity\RemoteController;
        $controller->setLabel($label);

        $template = $templateFactory->getParent(-3); // pick default template to be edited later
        $controller->setTemplate($template);

        $entityManager->persist($controller);
        $entityManager->persist($template);
        $entityManager->flush();

        return new JsonResponse(true);
    }

    /**
     * controller-delete
     */
    public function delete($id, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Entity\RemoteController::class);

        $controller = $repository->findOneBy(['id' => $id]);

        $entityManager->remove($controller);
        $entityManager->flush();

        return new JsonResponse(true);
    }

    /**
     * controller-save-buttons
     */
    public function saveButtons(Request $request, EntityManagerInterface $entityManager, Factory\TemplateFactory $templateFactory)
    {
        $controllerId = (int) $request->request->get('id');
        $templateId = (int) $request->request->get('controller-template');
        $btnArrangement = $request->request->get('button-arrangement');
        if (!isset($controllerId) || !isset($templateId) || !isset($btnArrangement)) {
            throw new \Exception("Missing fields: must POST id, controller-template, button-arrangement; received " . print_r($request->request->all(), true));
        }
        $btnArrangement = json_decode($btnArrangement, true);

        $controllerRepo = $entityManager->getRepository(Entity\RemoteController::class);
        $controller = $controllerRepo->find($controllerId);
        
        if ($templateId !== 0) { // not custom template
            $template = $templateFactory->cloneParent($templateId);
            // todo: set custom twig
            $entityManager->persist($template);
            $controller->setTemplate($template);
        } else {
            $template = $entityManager->getRepository(Entity\Template::class)->find($templateId);
        }
        $entityManager->persist($controller);

        $btnRepo = $entityManager->getRepository(Entity\Button::class);
        $twig = $template->getTwig();
        foreach ($btnArrangement as $twigKey => $btnId) {
            if (strpos($twig, $twigKey) === false) {
                throw new \Exception("Twig key {$twigKey} not found in template string:\n{$twig}\n\n");   
            }
            $btn = $btnRepo->find($btnId);
            $btn->setTwigKey($twigKey);
            $btn->addController($controller);
            $entityManager->merge($btn);
        }

        $entityManager->flush();
        return new JsonResponse(true);
    }
}
