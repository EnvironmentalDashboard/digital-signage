<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

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
