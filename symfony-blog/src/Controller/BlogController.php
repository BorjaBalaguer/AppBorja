<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(): Response
    {
        return $this->render('blog/blog.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }
    /**
     * @Route("/single_post", name="single_post")
     */
    public function post(): Response
    {
        return $this->render('blog/single_post.html.twig', [
            'controller_name' => 'BlogController',
        ]);
    }

}