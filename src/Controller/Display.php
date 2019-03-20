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
        return null;
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
