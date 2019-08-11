<?php
namespace App\Controller;

use App\Entity;
use App\Factory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Doctrine\ORM\EntityManagerInterface;

class Display extends AbstractController
{

    /**
     * display-present
     */
    public function present($id, Request $request, EntityManagerInterface $entityManager) {
        $ret = [];
        $presentations = $entityManager->getRepository(Entity\Display::class)->find($id)->getPresentations();
        $presCount = count($presentations);
        for ($i = 0; $i < $presCount; $i++) { // for all presentations associated with display $id
            $presentation = $presentations[$i];
            $style = $presentation->getTemplate()->getStyle();

            $carousels = [];
            $map = $presentation->getCarouselPresentationMaps();
            foreach ($map as $relation) { // for all carousels associated with $presentation
                $twigKey = $relation->getTemplateKey();
                $carousel = $relation->getCarousel();
                $processedFrames = $this->processFrames($carousel->getFrames(), $entityManager);

                $carousels[$twigKey] = $processedFrames;
            }

            $ret[$presentation->getId()] = [
                'id' => $presentation->getId(),
                'style' => $style,
                'carousels' => $carousels,
                'duration' => $presentation->getDuration(),
                'next' => ($i === $presCount - 1) ? $presentations[0]->getId() : $presentations[$i + 1]->getId()
            ];
        }
        return new JsonResponse($ret);
    }

    private function processFrames($frames, $entityManager) {
        $processedFrames = [];
        $frameCount = count($frames);
        for ($j = 0; $j < $frameCount; $j++) { // for all frames associated with $carousel
            $frame = $frames[$j];
            $nextIdx = ($j === $frameCount - 1) ? 0 : $j + 1;
            $nextFrame = $frames[$nextIdx];
            $url = $frame->getUrl();
            $parts = parse_url($url);
            switch ($parts['host']) {
                case 'www.youtube.com':
                    parse_str($parts['query'], $get_array);
                    $processedFrames[] = [
                        'id' => "frame{$frame->getId()}",
                        'url' => "https://www.youtube.com/embed/{$get_array['v']}",
                        'dur' => $frame->getDuration(),
                        'next' => "frame{$nextFrame->getId()}"
                    ];
                    break;
                default:
                    $processedFrames[] = [
                        'id' => "frame{$frame->getId()}",
                        'url' => $url,
                        'dur' => $frame->getDuration(),
                        'next' => "frame{$nextFrame->getId()}"
                    ];
            }
        }
        return $processedFrames;
    }

    /**
     * display-table
     */
    public function table(Request $request, EntityManagerInterface $entityManager, Factory\TemplateFactory $templateFactory)
    {
        $repository = $entityManager->getRepository(Entity\Display::class);
        $rendered = [];
        $frame_arrangements = [];
        $entities = $repository->findAll();
        foreach ($entities as $display) {
            $presentations = $display->getPresentations();
            // compile twig templates
            foreach ($presentations as $presentation) {
                $pres_id = $presentation->getId();
                $frame_arrangements[$pres_id] = [];
                $template_params = [];
                $twig = $presentation->getTemplate()->getTwig();
                $template = $this->get('twig')->createTemplate($twig);
                $map = $presentation->getCarouselPresentationMaps();
                foreach ($map as $i => $relation) {
                    $key = $relation->getTemplateKey();
                    $carousel = $relation->getCarousel();
                    $frame_arrangements[$pres_id][$key] = $carousel->getId();
                    $template_params[$key] = $carousel->getLabel();
                }
                $rendered[$pres_id][] = $template->render($template_params);
            }
            // setup blank presentation that can be edited for displays with no presentations
            if (count($presentations) === 0) {
                $template = $templateFactory->cloneParent(-1); // pick a default template
                $tmp = new Entity\Presentation;
                $tmp->setTemplate($template);
                $tmp->setDisplay($display);
                $tmp->setLabel('Temporary placeholder presentation');
                $display->addPresentation($tmp);
                if (empty($rendered['tmp'])) {
                    $twig = $template->getTwig();
                    $twig_template = $this->get('twig')->createTemplate($twig);
                    $rendered['tmp'] = $twig_template->render(['url1' => 'drag carousel here']);
                }
            }
        }

        return $this->render('display-table.html.twig', [
            'displays' => $entities,
            'carousels' => $rendered,
            'frame_arrangements' => $frame_arrangements,
            'templates' => $entityManager->getRepository(Entity\Template::class)->findPresentationTemplates()
        ]);
    }
    
    /**
     * carousel-list-by-display
     */
    public function carouselListByDisplay(Request $request, EntityManagerInterface $entityManager, $id)
    {
        $carousels = [];
        $display = $entityManager->getRepository(Entity\Display::class)->find($id);
        foreach ($display->getPresentations() as $pres) {
            foreach ($pres->getCarouselPresentationMaps() as $map) {
                $carousel = $map->getCarousel();
                // todo don't fetch duplicates from db
                $carousels[$carousel->getId()] = [
                    'id' => $carousel->getId(),
                    'label' => $carousel->getLabel()];
            }
        }
        // var_dump($button);die;

        return new JsonResponse(array_values($carousels));
    }

    /**
     * display-labels
     */
    public function listLabels(EntityManagerInterface $entityManager)
    {
        $response = [];
        foreach ($entityManager->getRepository(Entity\Display::class)->findAll() as $display) {
            $response[$display->getId()] = $display->getLabel();
        }

        return new JsonResponse($response);
    }
}
