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
     * controller-url
     */
    public function show($id, Request $request, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Entity\RemoteController::class);
        $controller = $repository->find($id);
        if (!$controller) {
            throw new \Exception("No remote controller with id {$id}");
        }

        $twigKeys = [];
        foreach ($controller->getButtons() as $button) {
            $buttonSrc = ($button->getType() === Entity\Button::PLAY) ? "{$this->generateUrl('index')}images/play.svg" : "{$this->generateUrl('index')}uploads/{$button->getImage()}";
            $twigKeys[$button->getTwigKey()] = "<img data-type='{$button->getType()}' data-url='{$button->getTriggerUrl()}' data-id='{$button->getId()}' src='{$buttonSrc}' />";
        }
        $twigKeys['controllerId'] = $id;
        $twig = '{% include "remote-controller-top.html.twig" %}' . $controller->getTemplate()->getTwig() . '{% include "remote-controller-bottom.html.twig" %}';
        $template = $this->get('twig')->createTemplate($twig);
        return new Response($template->render($twigKeys));
    }

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
            // todo instead of counting occurrences of {{ we should maybe get list of twig keys w regex like in DisplayEdit::savePresentations
            $buttonCount = substr_count($twig, '{{'); // the number of twig placeholders in the markup
            $counter = 1;
            $controllerButtons = [];
            // compile twig templates
            foreach ($buttons as $button) {
                $controllerButtons[$button->getTwigKey()] = "<img src='/digital-signage/uploads/{$button->getImage()}' class='img-fluid' style='max-height:100%' />";
                $buttonArrangement[$controllerId][$button->getTwigKey()] = $button->getId();
                $counter++;
            }
            for ($counter = 1; $counter <= $buttonCount; $counter++) { // if we're missing buttons create dummy ones so can still compile twig
                if (!array_key_exists("btn{$counter}", $controllerButtons)) {
                    $controllerButtons["btn{$counter}"] = "<div style='width:100%;height:100%'><svg viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'><text x='0' y='15'>drag button here</text></svg></div>";
                    $buttonArrangement[$controllerId]["btn{$counter}"] = 0;
                }
            }
            $rendered[] = $template->render($controllerButtons);
        }

        return $this->render('remote-controller-table.html.twig', [
            'controllers' => $entities,
            'markup' => $rendered,
            'button_arrangements' => $buttonArrangement,
            'displays' => $entityManager->getRepository(Entity\Display::class)->findAll(),
            'templates' => $entityManager->getRepository(Entity\Template::class)->findControllerTemplates()
        ]);
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

}
