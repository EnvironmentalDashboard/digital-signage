<?php

namespace App\Controller;

use App\Entity;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\ORM\EntityManagerInterface;

class CarouselEdit extends AbstractController
{
    /**
     * @Route("/carousel/create", name="carousel_create")
     */
    public function create(Request $request, EntityManagerInterface $entityManager)
    {
        $label = $request->request->get('label');

        /**
         * Create carousel
         */
        $carousel = new Entity\Carousel;
        $carousel->setLabel($label);

        $entityManager->persist($carousel);
        $entityManager->flush();

        /**
         * Response
         */
        return JsonResponse([
            'carousel' => [
                'id' => $carousel->getId(),
                'label' => $carousel->getLabel()
            ]
        ]);
    }

    /**
     * @Route("/carousel/{id}/delete", name="carousel_delete")
     */
    public function delete($id, EntityManagerInterface $entityManager)
    {
        /**
         * Delete carousel
         */
        $carousel = new Entity\Carousel;
        $carousel->setId($id);

        $entityManager->remove($carousel);
        $entityManager->flush();

        return JsonResponse(true);
    }

    /**
     * @Route("/foo", name="foo", methods={"GET"})
     */
    public function foo(Request $request)
    {
        return new Response('
            <form action="/carousel/create" method="post"><input type="text" name="label"><input type="submit" name="send"></form>
        ');
    }

    /**
     * @Route("/carousel/{id}/frames/save", name="saveFrames", methods={"POST"})
     */
    public function saveFrames(Request $request, $id)
    {
        $formBuilder = $this->createFormBuilder([]);

        $form = $formBuilder
            ->add('frames', CollectionType::class, [
                'entry_type' => Entity\Frame::class,
                'entry_options' => [
                    'url' => 'url',
                    'duration' => 'duration'
                ]
            ])
            ->getForm();

        //$form->submit($request->request->all());
        $form->handleRequest($request);

        return new JsonResponse([($form->isSubmitted() ? "yes" : "no"), $form->getData()]);
    }

    /**
     * @Route("/carousel/{id}/frames/", name="carousel_save_frames")
     */
    public function getFrames(Request $request, $id)
    {
    }
}
