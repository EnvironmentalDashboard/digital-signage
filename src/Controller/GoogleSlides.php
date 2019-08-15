<?php

namespace App\Controller;

use App\Entity;
use App\Service\ButtonManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;

class GoogleSlides extends AbstractController
{
    /**
     * google-slides-save
     */
    public function save(Request $request, EntityManagerInterface $entityManager, ButtonManager $buttonManager, $presentationId)
    {
        $data = json_decode($request->request->get('notes'), true);
        $carouselId = $request->request->get('carousel');
        if (!is_array($data) || $carouselId === null) {
            throw new \Exception('Invalid payload');
        }
        $response = [];
        // need to save buttons to remote controller if defined in presenter notes
        $remoteController = null;
        $buttons = [];
        $carousel = $entityManager->getRepository(Entity\Carousel::class)->find($carouselId);
        $repository = $entityManager->getRepository(Entity\Display::class);

        foreach ($data as $i => $value) {
            $response[$i] = [];

            preg_match("/duration:(\s*)(\d*)/i", $value, $matches);
            $dur = ($matches) ? round($matches[2]) : 7;
            $response[$i]['dur'] = $dur;

            preg_match("/url:(\s*)(.*)/i", $value, $matches);
            if ($matches) {
                $url = $matches[2];
            } else {
                $url = "https://docs.google.com/presentation/d/{$presentationId}/preview?rm=minimal#slide=" . ($i + 1);
            }
            $response[$i]['url'] = $url;

            // create frame to link to buttons
            $frame = new Entity\Frame;
            $frame->setCarousel($carousel);
            $frame->setDuration($dur * 1000);
            $frame->setUrl($url);
            $entityManager->persist($frame);
            $entityManager->flush();
            $response[$i]['frame'] = $frame->getId();

            preg_match("/button:(\s*)(.*)/i", $value, $matches);
            preg_match("/display:(\s*)(.*)/i", $value, $matches2);
            if ($matches && $matches2) {
                $buttonText = $matches[2];
                $displayLabel = $matches2[2];
                $display = $repository->findOneBy(['label' => $displayLabel]);
                if ($display !== null) {
                    if ($remoteController === null) {
                        $remoteController = new Entity\RemoteController;
                        $remoteController->setLabel("Controller for {$presentationId}");
                    }
                    $buttons[$i] = ['display' => $display, 'button' => $buttonText, 'frame' => $frame];
                }
            }
        }

        foreach ($buttons as $i => $button) {
            $fn = uniqid() . '.svg';
            $size = file_put_contents("/tmp/{$fn}", '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="100%" height="100%" viewBox="0 0 1920 1080"><text x="'.(strlen($button['button']) * 0.2).'%" y="50%" style="font-size:10rem;font-family:sans-serif">'.$button['button'].'</text></svg>');
            $image = new UploadedFile("/tmp/{$fn}", $fn, 'image/svg', $size, null, true);
            $button = $buttonManager->create(Entity\Button::TRIGGER_FRAME, $button['display'], $button['frame'], $remoteController, $image, null);
            $button->setTwigKey('btn' . ($i + 1));
            $entityManager->persist($button);
        }

        if ($remoteController !== null) {
            $template = new Entity\Template;
            $twig = '';
            $buttonCount = count($buttons);
            for ($i = 0; $i < $buttonCount; $i++) { 
                $twig .= '<div class="button" data-twig="btn'.($i+1).'" style="height:50%;width:25%;">{{ btn'.($i+1).'|raw }}</div>';
            }
            $template->setTwig($twig);
            $template->setLabel("Auto-generated {$buttonCount} button template");
            $remoteController->setTemplate($template);
            $entityManager->persist($template);
            $entityManager->persist($remoteController);
        }

        $entityManager->flush();

        return new JsonResponse($response);
    }

}
