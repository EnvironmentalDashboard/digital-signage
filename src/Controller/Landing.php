<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Landing extends AbstractController
{
    /**
     * index page
     */
    public function index()
    {
        return $this->render('index.html.twig');
    }
}
