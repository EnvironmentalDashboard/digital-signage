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

class RemoteController extends AbstractController
{
    /**
     * controller-table-all
     */
    public function table(Request $request, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Entity\RemoteController::class);
        $rendered = [];
        $button_arrangements = [];
        $entities = $repository->findAll();

        foreach ($entities as $controller) {
            $buttons = $controller->getButtons();
            $twig = $controller->getTemplate()->getTwig();
            $template = $this->get('twig')->createTemplate($twig);
            $button_arrangements = [];
            // compile twig templates
            foreach ($buttons as $i => $button) {
                $button_arrangements['btn'.($i+1)] = "trigger {$button->getTriggerFrame()->getUrl()}";
            }
            // setup blank button that can be edited for controllers with no buttons
            if (count($buttons) === 0) {
                $button_arrangements['btn1'] = "placeholder";
            }
            $rendered[] = $template->render($button_arrangements);
        }

        return $this->render('remote-controller-table.html.twig', ['controllers' => $entities, 'markup' => $rendered]);
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
            default:
                $template_id = 3;
                break;
        }
        $template = $templateFactory->fromParent($template_id);
        $twig = $template->getTwig();
        $template = $this->get('twig')->createTemplate($twig);
        return new Response($template->render(['btn1' => 'drag button here', 'btn2' => 'drag button here'])); // need to include all possible twig keys
    }
}
