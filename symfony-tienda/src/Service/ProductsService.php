<?php
namespace App\Service;

use App\Entity\Product;
use App\Service\CartService;
use Doctrine\Persistence\ManagerRegistry;

class ProductsService{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine, CartService $cart)
    {
        $this->doctrine = $doctrine;
        $this->cart = $cart;
    }
    public function getProducts(): ?array{
        $repository = $this->doctrine->getRepository(Product::class);
        return $repository->findAll();
    }
    public function getCartProducts(){
  
        $repository = $this->doctrine->getRepository(Product::class);
        $products = $repository->getFromCart($this->cart);
        $data = [];
       
        foreach($products  as $key => $product){
            $data[] = [
                "id"=> $product->getId(),
                "name" => $product->getName(),
                "price" => $product->getPrice(),
                "photo" => $product->getPhoto(),
                "quantity" => $this->cart->getCart()[$product->getId()]
             ];
        }
        
        return $data;
    }
}