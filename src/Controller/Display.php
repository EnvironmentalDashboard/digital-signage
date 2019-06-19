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

        $presentations = [];
        foreach ($entityManager->getRepository(Entity\Display::class)->find($id)->getPresentations() as $presentation) {
            $template = $this->get('twig')->createTemplate(
                $presentation->getTemplate()->getTwig()
            );

            $carousels = [];
            $templateParams = [];
            $map = $presentation->getCarouselPresentationMaps();
            foreach ($map as $relation) {
                $twigKey = $relation->getTemplateKey();
                $carousel = $relation->getCarousel();
                $frames = [];
                foreach ($carousel->getFrames() as $frame) {

                    $url = $frame->getUrl();
                    $parts = parse_url($url);
                    switch ($parts['host']) {
                        case 'www.youtube.com':
                            parse_str($parts['query'], $get_array);
                            $frames[] = [
                                'id' => "frame{$frame->getId()}",
                                'url' => "https://www.youtube.com/embed/{$get_array['v']}",
                                'dur' => $frame->getDuration(),
                            ];
                            break;
                        case 'docs.google.com':
                            preg_match('#/presentation/d/(.*?)/edit#', $parts['path'], $matches);
                            if (!empty($matches)) {
                                $presId = $matches[1];
                                $repository = $entityManager->getRepository(Entity\GoogleSlides::class);
                                $googleSlides = $repository->findOneBy(['presentationId' => $presId]);
                                if ($googleSlides === null) { // uh oh, somehow presentation duration got deleted
                                    $frames[] = [
                                        'id' => "frame{$frame->getId()}",
                                        'url' => "https://docs.google.com/presentation/d/{$presId}/preview?rm=minimal",
                                        'dur' => $frame->getDuration(),
                                    ];
                                    break;
                                }
                                foreach ($googleSlides->getData() as $i => $dur) {
                                    $id = ($i === 0) ? "frame{$frame->getId()}" : "frame{$frame->getId()}-{$i}"; // buttons trigger frames with document.getElementById('frame' + frameId), so give first slide this special id to be found
                                    $i = $i + 1;
                                    $frames[] = [
                                        'id' => $id,
                                        'url' => "https://docs.google.com/presentation/d/{$presId}/preview?rm=minimal#slide={$i}",
                                        'dur' => $dur,
                                    ];
                                }
                                break;
                            }
                        default:
                            $frames[] = [
                                'id' => "frame{$frame->getId()}",
                                'url' => $url,
                                'dur' => $frame->getDuration(),
                            ];
                    }
                }
                $carousels[$twigKey] = $frames;
                $templateParams[$twigKey] = "<iframe id='pres{$presentation->getId()}-{$twigKey}-primary' src='about:blank' frameborder='0'></iframe><iframe id='pres{$presentation->getId()}-{$twigKey}-secondary' src='about:blank' frameborder='0'></iframe>";
            }
            $markup = $template->render($templateParams);

            $presentations[$presentation->getId()] = [
                'id' => $presentation->getId(),
                'template' => $markup,
                'carousels' => $carousels,
                'duration' => $presentation->getDuration()
            ];
        }

        return $this->render('present.html.twig', ['presentations' => 
            $presentations
        ]);

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
    
    private function iframeMarkup($i, $frame, $entityManager) {
        $hidden = ($i === 0) ? 'class="fade-in"' : 'class="fade-out"';
        $url = $frame->getUrl();
        $parts = parse_url($url);
        switch ($parts['host']) {
            case 'www.youtube.com':
                parse_str($parts['query'], $get_array);
                return "<iframe src='about:blank' data-src='https://www.youtube.com/embed/{$get_array['v']}' id='frame{$frame->getId()}' data-duration='{$frame->getDuration()}' frameborder='0' {$hidden}></iframe>";
            case 'docs.google.com':
                preg_match('#/presentation/d/(.*?)/edit#', $parts['path'], $matches);
                if (empty($matches)) {
                    break;
                }
                $presId = $matches[1];
                $repository = $entityManager->getRepository(Entity\GoogleSlides::class);
                $googleSlides = $repository->findOneBy(['presentationId' => $presId]);
                if ($googleSlides === null) {
                    return "<iframe src='about:blank' data-src='https://docs.google.com/presentation/d/{$presId}/preview?rm=minimal' id='frame{$frame->getId()}' data-duration='{$frame->getDuration()}' frameborder='0' {$hidden}></iframe>";
                }
                $iframes = [];
                foreach ($googleSlides->getData() as $key => $value) {
                    $id = ($key === 0) ? "frame{$frame->getId()}" : "frame{$frame->getId()}-{$key}"; // buttons trigger frames with document.getElementById('frame' + frameId), so give first slide this special id to be found
                    $key = $key + 1;
                    $iframes[] = "<iframe src='about:blank' data-src='https://docs.google.com/presentation/d/{$presId}/preview?rm=minimal#slide={$key}' id='{$id}' data-duration='{$value}' frameborder='0' {$hidden}></iframe>";
                    $hidden = 'class="fade-out"';
                }
                return implode('', $iframes);
        }
        return "<iframe src='about:blank' data-src='{$url}' id='frame{$frame->getId()}' data-duration='{$frame->getDuration()}' frameborder='0' {$hidden}></iframe>";
    }
}
