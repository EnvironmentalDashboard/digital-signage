<?php

namespace App\Controller;

use App\Entity;

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
                foreach ($carousel->getFrames() as $frame) {
                    $iframes[] = "<iframe src='{$frame->getUrl()}' data-duration='{$frame->getDuration()}' frameborder='0' style='width: 100%;height: 100%;'></iframe>";
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
    public function table(Request $request, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Entity\Display::class);
        
        $entities = $repository->findAll();

        return $this->render('display-table.html.twig', ['displays' => $entities]);
    }

    /**
     * display-templates
     */
    public function template(Request $request, EntityManagerInterface $entityManager, $name)
    {
        // TODO: grab $id template as string from db
        $str = "display-templates/{$name}.html.twig";
        return $this->render($str);
    }
}
