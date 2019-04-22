<?php
namespace App\Controller;

use App\Entity;
use App\Factory;

use Symfony\Component\Routing\Annotation\Route;
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
					$iframes[] = $this->iframeMarkup($i, $frame, $entityManager);
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
				$template = $templateFactory->fromParent(1); // pick a default template
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
        return new Response($template->render(['url1' => 'drag carousel here', 'url2' => 'drag carousel here'])); // need to include all possible twig keys
	}
	
	private function iframeMarkup($i, $frame, $entityManager) {
		$hidden = ($i === 0) ? '' : 'display:hidden';
		$url = $frame->getUrl();
		$parts = parse_url($url);
		switch ($parts['host']) {
			case 'www.youtube.com':
				parse_str($parts['query'], $get_array);
				return "<iframe src='https://www.youtube.com/embed/{$get_array['v']}' id='frame{$frame->getId()}' data-duration='{$frame->getDuration()}' frameborder='0' style='width: 100%;height: 100%;{$hidden}'></iframe>";
			case 'docs.google.com':
				preg_match('#/presentation/d/(.*?)/edit#', $parts['path'], $matches);
				if (empty($matches)) {
					break;
				}
				$presId = $matches[1];
				$repository = $entityManager->getRepository(Entity\GoogleSlides::class);
				$googleSlides = $repository->findOneBy(['presentationId' => $presId]); // TODO: make presentationId column unique
				$iframes = [];
				foreach ($googleSlides->getData() as $key => $value) {
					$key = $key + 1;
					$iframes[] = "<iframe src='https://docs.google.com/presentation/d/{$presId}/preview#slide={$key}' id='frame{$frame->getId()}-{$key}' data-duration='{$value}' frameborder='0' style='width: 100%;height: 100%;{$hidden}'></iframe>";
				}
				return implode('', $iframes);
		}
		return "<iframe src='{$url}' id='frame{$frame->getId()}' data-duration='{$frame->getDuration()}' frameborder='0' style='width: 100%;height: 100%;{$hidden}'></iframe>";
	}
}
