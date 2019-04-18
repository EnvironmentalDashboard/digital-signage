<?php

namespace App\Controller;

use App\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Service\Youtube as YoutubeService;

class Youtube extends AbstractController
{
    /**
     * youtube-video-length
     */
    public function length(Request $request, YoutubeService $service, $videoId)
    {
        return new JsonResponse($service->videoLength($videoId));
    }
}
