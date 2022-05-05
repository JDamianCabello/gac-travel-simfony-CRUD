<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Products;
use App\Entity\StockHistory;
use DateTimeImmutable;
use Doctrine\DBAL\Types\BigIntType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    public function index(): Response
    {
        $products = $this->getDoctrine()->getRepository(Products::class)->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    public function delete($id) {

        $entityManager = $this->getDoctrine()->getManager();
        $product = $this->getDoctrine()->getRepository(Products::class);
        $product = $product->find($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No existe un producto con el id: ' . $id
            );
        }

        $entityManager->remove($product);
        try {
            $entityManager->flush();
        } catch (\Throwable $th) {
            $this->addFlash('error','Error al eliminar el producto. Hay que eliminar las entradas del log para borrarlo');
            return $this->redirectToRoute('app_product');
        }

        $this->addFlash('alert','Producto eliminado con éxito');


        return $this->redirectToRoute('app_product');

    }

    public function create(Request $request) {

        $categorys = $this->getDoctrine()->getRepository(Categories::class)->findAll();

        $product = new Products();

        $form = $this->createFormBuilder($product)
            ->add('name', TextType::class)
            ->add('category_id', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => function ($category) {
                    return $category->getName();
                    }
                ])
            ->add('stock', NumberType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $product = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $product->setCreatedAt(new DateTimeImmutable(date('Y-m-d H:i:s')));
            $em->persist($product);

            $log = new StockHistory();
            $log->setUserId($this->getUser());
            $log->setCreatedAt(new DateTimeImmutable(date('Y-m-d H:i:s')));
            $log->setStock($form->get('stock')->getData());
            $log->setProductId($product);
            $em->persist($log);

            $em->flush();

            $this->addFlash('alert','Producto creado con éxito');
            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function update(Request $request, $id) {

        $product = $this->getDoctrine()->getRepository(Products::class);
        $product = $product->find($id);

        if (!$product) {
            throw $this->createNotFoundException(
                'No existe una categoría con el id: ' . $id
            );
        }

        $form = $this->createFormBuilder($product)
            ->add('name', TextType::class)
            ->add('category_id', EntityType::class, [
                'class' => Categories::class,
                'choice_label' => function ($category) {
                    return $category->getName();
                    }
                ])
            ->add('stock', NumberType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $product = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            
            $log = new StockHistory();
            $log->setUserId($this->getUser());
            $log->setCreatedAt(new DateTimeImmutable(date('Y-m-d H:i:s')));
            $log->setStock($form->get('stock')->getData());
            $log->setProductId($product);
            $em->persist($log);
            
            $em->flush();
            

            $this->addFlash('alert','Producto actualizado con éxito');

            return $this->redirectToRoute('app_product');
        }

        return $this->render(
            'product/edit.html.twig',
            array('form' => $form->createView())
        );

    }
}
