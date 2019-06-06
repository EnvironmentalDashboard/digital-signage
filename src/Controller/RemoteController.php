<?php

namespace App\Controller;

use App\Entity;
use App\Factory;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\EntityManagerInterface;

class RemoteController extends AbstractController
{

    /**
     * remote-controller
     */
    public function getController($id, Request $request, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Entity\RemoteController::class);
        $controller = $repository->find($id);
        $buttons = $controller->getButtons();
        $twigKeys = [];
        foreach ($buttons as $button) {
            $twigKeys[$button->getTwigKey()] = $button->getTriggerFrame()->getUrl();
        }
        
        $twig = $controller->getTemplate()->getTwig();
        $template = $this->get('twig')->createTemplate($twig);
        return $template->render($twigKeys);
    }

    /**
     * controller-table
     */
    public function table(Request $request, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Entity\RemoteController::class);
        $rendered = [];
        $buttonArrangement = [];
        $entities = $repository->findAll();

        foreach ($entities as $controller) {
            $controllerId = $controller->getId();
            $buttons = $controller->getButtons();
            $twig = $controller->getTemplate()->getTwig();
            $template = $this->get('twig')->createTemplate($twig);
            $buttonCount = substr_count($twig, '{{'); // the number of twig placeholders in the markup
            $counter = 1;
            $controllerButtons = [];
            // compile twig templates
            foreach ($buttons as $button) {
                $controllerButtons["btn{$counter}"] = "trigger {$button->getTriggerFrame()->getUrl()}";
                $buttonArrangement[$controllerId]["btn{$counter}"] = $button->getId();
                $counter++;
            }
            for (; $counter <= $buttonCount; $counter++) { // if we're missing buttons create dummy ones so can still compile twig
                $controllerButtons["btn{$counter}"] = "placeholder";
                $buttonArrangement[$controllerId]["btn{$counter}"] = 0;
            }
            $rendered[] = $template->render($controllerButtons);
        }

        return $this->render('remote-controller-table.html.twig', ['controllers' => $entities, 'markup' => $rendered, 'button_arrangements' => $buttonArrangement, 'displays' => $entityManager->getRepository(Entity\Display::class)->findAll()]);
    }

    /**
     * button-list-by-controller
     */
    public function listByController(Request $request, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Entity\RemoteController::class);
        $entities = $repository->findAll();
        return $this->render('remote-controller-list.html.twig', ['controllers' => $entities]);
    }

    /**
     * controller-save-buttons
     */
    public function saveButtons(Request $request, EntityManagerInterface $entityManager)
    {
    }

    /**
     * controller-template
     */
    public function template(Request $request, EntityManagerInterface $entityManager, Factory\TemplateFactory $templateFactory, $name)
    {
        switch ($name) {
            case '2 Buttons':
                $templateId = -3;
                break;
            case '4 Buttons':
                $templateId = -4;
                break;
            case '8 Buttons':
                $templateId = -5;
                break;
            default:
                $templateId = -3;
                break;
        }

        $template = $templateFactory->getParent($templateId);
        $twig = $template->getTwig();
        $template = $this->get('twig')->createTemplate($twig);
        return new Response($template->render(
            array_fill_keys(array_map( // need to include all possible twig keys
                function ($n) { return "btn{$n}"; },
                range(1, 8)
            ), 'drag button here')
        ));
    }
}
