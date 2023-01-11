<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ProductsService;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'product')]
    public function index(ProductsService $productsService): Response
    {
        $products = $productsService->getProducts();
        return $this->render('product/product.html.twig', compact('products'));
    }
}
