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
     * display-url
     */
    public function present($id, Request $request, EntityManagerInterface $entityManager)
    {
        $ret = [];
        $presentations = $entityManager->getRepository(Entity\Display::class)->find($id)->getPresentations();
        $presCount = count($presentations);
        for ($i = 0; $i < $presCount; $i++) { // for all presentations associated with display $id
            $presentation = $presentations[$i];
            $template = $this->get('twig')->createTemplate(
                $presentation->getTemplate()->getTwig()
            );

            $carousels = [];
            $templateParams = [];
            $map = $presentation->getCarouselPresentationMaps();
            foreach ($map as $relation) { // for all carousels associated with $presentation
                $twigKey = $relation->getTemplateKey();
                $carousel = $relation->getCarousel();
                $processedFrames = $this->processFrames($carousel->getFrames(), $entityManager);

                $carousels[$twigKey] = $processedFrames;
                $templateParams[$twigKey] = "<iframe id='pres{$presentation->getId()}-{$twigKey}-primary' src='about:blank' frameborder='0'></iframe><iframe id='pres{$presentation->getId()}-{$twigKey}-secondary' src='about:blank' frameborder='0'></iframe>";
            }
            
            $markup = $template->render($templateParams);

            $ret[$presentation->getId()] = [
                'id' => $presentation->getId(),
                'template' => $markup,
                'carousels' => $carousels,
                'duration' => $presentation->getDuration(),
                'next' => ($i === $presCount - 1) ? $presentations[0]->getId() : $presentations[$i + 1]->getId()
            ];
        }
        return $this->render('present.html.twig', ['presentations' => 
            $ret
        ]);
    }

    /**
     * display-url-json
     */
    public function presentJson($id, Request $request, EntityManagerInterface $entityManager) {
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
                case 'docs.google.com':
                    preg_match('#/presentation/d/(.*?)/edit#', $parts['path'], $matches);
                    if (!empty($matches)) {
                        $presId = $matches[1];
                        $repository = $entityManager->getRepository(Entity\GoogleSlides::class);
                        $googleSlides = $repository->findOneBy(['presentationId' => $presId]);
                        if ($googleSlides === null) { // uh oh, somehow presentation duration got deleted
                            $processedFrames[] = [
                                'id' => "frame{$frame->getId()}",
                                'url' => "https://docs.google.com/presentation/d/{$presId}/preview?rm=minimal",
                                'dur' => $frame->getDuration(),
                                'next' => "frame{$nextFrame->getId()}"
                            ];
                            break;
                        }
                        $slides = $googleSlides->getData();
                        $googleSlidesCount = count($slides) - 1;
                        foreach ($slides as $k => $dur) {
                            $id = ($k === 0) ? "frame{$frame->getId()}" : "frame{$frame->getId()}-{$k}"; // buttons trigger frames with document.getElementById('frame' + frameId), so give first slide this special id to be found
                            $nextId = ($k === $googleSlidesCount) ? "frame{$nextFrame->getId()}" : "frame{$nextFrame->getId()}-" . ($k + 1);
                            $k++;
                            $processedFrames[] = [
                                'id' => $id,
                                'url' => "https://docs.google.com/presentation/d/{$presId}/preview?rm=minimal#slide={$k}",
                                'dur' => $dur,
                                'next' => $nextId
                            ];
                        }
                        break;
                    }
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

        return $this->render('display-table.html.twig', ['displays' => $entities,
                                                        'carousels' => $rendered,
                                                        'frame_arrangements' => $frame_arrangements]);
    }

    /**
     * display-template
     */
    public function template(Request $request, EntityManagerInterface $entityManager, Factory\TemplateFactory $templateFactory, $name)
    {
        switch ($name) {
            case 'fullscreen':
                $template_id = -1;
                break;
            
            case 'marquee':
                $template_id = -2;
                break;
            
            default:
                $template_id = -1;
                break;
        }
        $template = $templateFactory->getParent($template_id);
        $twig = $template->getTwig();
        $template = $this->get('twig')->createTemplate($twig);
        return new Response($template->render(['url1' => 'drag carousel here', 'url2' => 'drag carousel here'])); // need to include all possible twig keys
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
}
