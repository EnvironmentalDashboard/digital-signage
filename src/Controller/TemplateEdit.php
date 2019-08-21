<?php

namespace App\Controller;

use App\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\EntityManagerInterface;

class TemplateEdit extends AbstractController
{
    /**
     * template-create
     */
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $label = $request->request->get('template-label');

        // Create template
        $template = new Entity\Template;
        $template->setLabel($label);

        $entityManager->persist($template);
        $entityManager->flush();

        return new JsonResponse(true);
    }

    /**
     * template-delete
     */
    public function delete($id, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Entity\Template::class);

        // Delete template
        $template = $repository->find($id);

        $entityManager->remove($template);
        $entityManager->flush();

        return new JsonResponse(true);
    }

    /**
     * template-save-placeholders
     */
    public function save($id, EntityManagerInterface $entityManager)
    {
        $repository = $entityManager->getRepository(Entity\Template::class);

        $template = $repository->find($id);
        $template->setTwig();

        $entityManager->merge($template);
        $entityManager->flush();

        return new JsonResponse(true);
    }

    
}
