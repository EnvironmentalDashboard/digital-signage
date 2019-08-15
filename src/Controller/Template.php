<?php

namespace App\Controller;

use App\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\EntityManagerInterface;

class Template extends AbstractController
{

    /**
     * template-table-all
     */
    public function table(Request $request, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Entity\Template::class);
        $templates = $repository->findAll();
        foreach ($templates as $template) {
            $templateString = $template->getTwig();
            preg_match_all('/\{\%\s*(.*)\s*\%\}|\{\{(?!%)\s*((?:[^\s])*)\s*(?<!%)\}\}/i', $templateString, $matches);
            for ($i = 0; $i < count($matches[0]); $i++) { 
                $templateString = str_replace('data-twig="'.explode('|', $matches[2][$i])[0].'" style="', 'style="display: inline-flex;flex-flow: row wrap;justify-content: center;align-items: center;', $templateString);
                $templateString = str_replace($matches[0][$i], '<svg style="width: 100%;height: 100%;cursor: pointer;position: relative;border: 1px dashed #333" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"> <text x="15" y="15">Drop zone</text> </svg>', $templateString);
            }
            $template->setTwig($templateString);
        }
        return $this->render('template-table.html.twig', ['templates' => $templates]);
    }
}
