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

class Display extends AbstractController
{

    /**
     * display-url
     */
    public function present($id, Request $request, EntityManagerInterface $entityManager)
    {
        $cycle = [];
        $pres_durations = [];
        $displayRepository = $entityManager->getRepository(Entity\Display::class);
        $display = $displayRepository->find($id);

        foreach ($display->getPresentations() as $presentation) {
            $twig = $presentation->getTemplate()->getTwig();
            $template = $this->get('twig')->createTemplate($twig);
            $pres_carousels = [];
            $pres_durations[$presentation->getId()] = $presentation->getDuration();

            $map = $presentation->getCarouselPresentationMaps();
            foreach ($map as $relation) {
                $key = $relation->getTemplateKey();
                $carousel = $relation->getCarousel();
                $iframes = [];
                foreach ($carousel->getFrames() as $i => $frame) {
                    $hidden = ($i === 0) ? '' : 'display:hidden';
                    $iframes[] = "<iframe sandbox='allow-scripts allow-same-origin allow-pointer-lock' src='{$frame->getUrl()}' data-duration='{$frame->getDuration()}' frameborder='0' style='width: 100%;height: 100%;{$hidden}'></iframe>";
                }
                $pres_carousels[$key] = implode('', $iframes);
            }
            $rendered = $template->render($pres_carousels);
            $cycle[] = ['id' => $presentation->getId(), 'markup' => $rendered];
        }

        return $this->render('present.html.twig', ['cycle' => $cycle, 'pres_durations' => $pres_durations]);
    }

    /**
     * display-table
     */
    public function table(Request $request, EntityManagerInterface $entityManager, Factory\TemplateFactory $templateFactory)
    {
        $repository = $entityManager->getRepository(Entity\Display::class);
        $rendered = [];
        $entities = $repository->findAll();
        foreach ($entities as $display) {
            $presentations = $display->getPresentations();
            // compile twig templates
            foreach ($presentations as $presentation) {
                $template_params = [];
                $twig = $presentation->getTemplate()->getTwig();
                $template = $this->get('twig')->createTemplate($twig);
                $map = $presentation->getCarouselPresentationMaps();
                foreach ($map as $i => $relation) {
                    $key = $relation->getTemplateKey();
                    $frames = $relation->getCarousel()->getFrames()->getValues();
                    $urls = [];
                    foreach ($frames as $frame) {
                        $urls[] = $frame->getUrl();
                    }
                    $template_params[$key] = implode(', ', $urls);
                }
                $rendered[$presentation->getId()][] = $template->render($template_params);
            }
            // setup blank presentation that can be edited for displays with no presentations
            if (count($presentations) === 0) {
                $template = $templateFactory->fromParent(1); // pick a default template
                $twig = $template->getTwig();
                $template = $this->get('twig')->createTemplate($twig);
                $rendered["d{$display->getId()}"][] = $template->render(['url1' => 'drag carousel here']);
            }
        }

        return $this->render('display-table.html.twig', ['displays' => $entities, 'carousels' => $rendered]);
    }

    /**
     * display-templates
     */
    public function template(Request $request, EntityManagerInterface $entityManager, Factory\TemplateFactory $templateFactory, $name)
    {
        switch ($name) {
            case 'fullscreen':
                $template_id = 1;
                break;
            
            case 'marquee':
                $template_id = 2;
                break;
            
            default:
                $template_id = 1;
                break;
        }
        $template = $templateFactory->fromParent($template_id);
        $twig = $template->getTwig();
        $template = $this->get('twig')->createTemplate($twig);
        return new Response($template->render(['url1' => 'drag carousel here', 'url2' => 'drag carousel here']));
    }
}
