<?php

namespace App\Controller;

use App\Entity\Autor;
use App\Entity\Libro;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class LibroController extends AbstractController
{
    /**
     * @Route("/libro/nuevo", name="nuevo_libro")
     */
    public function nuevo(ManagerRegistry $doctrine, Request $request){
        $libro = new Libro();

        $formulario = $this->createFormBuilder($libro)
            ->add('titulo', TextType::class)
            ->add('isbn', TextType::class)
            ->add('editorial', TextType::class, array('label' => 'Editorial'))
            ->add('autor', EntityType::class, array('class' => Autor::class, 'choice_label' => 'nombre',))
            ->add('save', SubmitType::class, array('label' => 'Enviar'))
            ->getForm();
            $formulario->handleRequest($request);

            if ($formulario->isSubmitted() && $formulario->isValid()) {
                $libro = $formulario->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($libro);
                $entityManager->flush();
                return $this->redirectToRoute('ficha_libro', ["codigo" => $libro->getId()]);
            }
            return $this->render('nuevo.html.twig', array('formulario' => $formulario->createView()));
    }
    
    /**
     * @Route("/libro/editar/{codigo}", name="editar_libro", requirements={"codigo"="\d+"})
     */
    public function editar(ManagerRegistry $doctrine, Request $request, $codigo){
        
        $repositorio = $doctrine->getRepository(Libro::class);
        $libro = $repositorio->find($codigo);

        $formulario = $this->createFormBuilder($libro)
            ->add('titulo', TextType::class)
            ->add('isbn', TextType::class)
            ->add('editorial', TextType::class, array('label' => 'Editorial'))
            ->add('autor', EntityType::class, array('class' => Autor::class, 'choice_label' => 'nombre',))
            ->add('save', SubmitType::class, array('label' => 'Enviar'))
            ->getForm();
            $formulario->handleRequest($request);

            if ($formulario->isSubmitted() && $formulario->isValid()) {
                $libro = $formulario->getData();
                $entityManager = $doctrine->getManager();
                $entityManager->persist($libro);
                $entityManager->flush();
                return $this->redirectToRoute('ficha_libro', ["codigo" => $libro->getId()]);
            }
            return $this->render('nuevo.html.twig', array('formulario' => $formulario->createView()));
    }
    //Comentario prueba
    
    private $libros = [

        1 => ["titulo" => "Contando Atardeceres", "isbn" => "8448031121", "editorial" => "Libros Cúpula"],

        2 => ["titulo" => "Todo Arde", "isbn" => "4681237854", "editorial" => "Ediciones B"],

        5 => ["titulo" => "Las Madres", "isbn" => "7854621548", "editorial" => "Alfaguara"],

        7 => ["titulo" => "Invisible", "isbn" => "8413145846", "editorial" => "Nube de tinta"],

        9 => ["titulo" => "La Invasión", "isbn" => "2345618794", "editorial" => "Alianza"]

    ];
    
    /**
     * @Route("/libro/insertar", name="insertar_libro")
     */
    public function insertar(ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        foreach($this->libros as $c){
            $libro = new Libro();
            $libro->setTitulo($c["titulo"]);
            $libro->setIsbn($c["isbn"]);
            $libro->setEditorial($c["editorial"]);
            $entityManager->persist($libro);
        }

        try
        {
            $entityManager->flush();
            return new Response("Libros insertados");
        } catch (\Exception $e){
            return new Response("Error insertando objetos");
        }
    }
    /**
     * @Route("/libro/{codigo<\d+>?1}", name="ficha_libro")
     */
    public function ficha(ManagerRegistry $doctrine, $codigo): Response{
        $repositorio = $doctrine->getRepository(Libro::class);
        $libro = $repositorio->find($codigo);
        
        return $this->render('ficha_libro.html.twig', ['libro' => $libro]);
    }

    /**
     * @Route("/libro/buscar/{texto}", name="buscar_libro")
     */
    public function buscar(ManagerRegistry $doctrine, $texto): Response{
        $repositorio = $doctrine->getRepository(Libro::class);

        $libros = $repositorio->findByName($texto);

        return $this->render('lista_libros.html.twig', ['libros' => $libros]);
    }

    /** 
     * @Route("/libro/update/{id}/{titulo}", name="modificar_libro")
    */
    public function update(ManagerRegistry $doctrine, $id, $titulo): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Libro::class);
        $libro = $repositorio->find($id);
        if ($libro){
            $libro->setNombre($titulo);
            try
            {
                $entityManager->flush();
                return $this->render('ficha_libro.html.twig', ['libro' => $libro]);
            } catch (\Exception $e){
                return new Response("Error insertando objetos");
            }
        } else
                return $this->render('ficha_libro.html.twig', ['libro' => null]);
    }

    /** 
     * @Route("/libro/delete/{id}", name="eliminar_libro")
    */
    public function delete(ManagerRegistry $doctrine, $id): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Libro::class);
        $libro = $repositorio->find($id);
        if ($libro){
            try
            {
                $entityManager->remove($libro);
                $entityManager->flush();
                return new Response("Libro eliminado");
            } catch (\Exception $e){
                return new Response("Error eliminado objeto");
            }
        } else
                return $this->render('ficha_libro.html.twig', ['libro' => null]);
    }

    /**
     * @Route("/libro/insertarConAutor", name="insertar_con_autor_libro")
     */
    public function insertarConAutor(ManagerRegistry $doctrine): Response{
        $entityManager = $doctrine->getManager();
        $autor = new Autor();

        $autor->setNombre("Jose Coronado");
        $libro = new Libro();

        $libro->setTitulo("Inseración de prueba con autor");
        $libro->setIsbn("900220022");
        $libro->setEditorial("insercion.de.prueba.autor@libro.es");
        $libro->setautor($autor);

        $entityManager->persist($autor);
        $entityManager->persist($libro);

        $entityManager->flush();
        return $this->render('ficha_libro.html.twig', ['libro' => $libro]);
    }

    /**
     * @Route("/libro/insertarSinAutor", name="insertar_sin_autor_libro")
     */
    public function insertarSinAutor(ManagerRegistry $doctrine): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Autor::class);

        $autor = $repositorio->findOneBy(["nombre" => "Jose Coronado"]);

        $libro = new Libro();

        $libro->setTitulo("Inseración de prueba sin autor");
        $libro->setIsbn("900220022");
        $libro->setEditorial("insercion.de.prueba.autor@libro.es");
        $libro->setAutor($autor);

        $entityManager->persist($libro);

        $entityManager->flush();
        return $this->render('ficha_libro.html.twig', ['libro' => $libro]);
    }
}