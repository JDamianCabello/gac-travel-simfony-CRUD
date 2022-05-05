<?php

namespace App\Controller;

use App\Entity\Categories;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    public function index(): Response
    {
        $categorys = $this->getDoctrine()->getRepository(Categories::class)->findAll();;


        return $this->render('category/index.html.twig', [
            'categorys' => $categorys
        ]);
    }

    public function create(Request $request) {

        $categorys = new Categories();

        $form = $this->createFormBuilder($categorys)
            ->add('name', TextType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $categorys = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $categorys->setCreatedAt(new DateTimeImmutable(date('Y-m-d H:i:s')));
            $em->persist($categorys);
            $em->flush();

            $this->addFlash('alert','Categoría creada con éxito');
            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function delete($id) {

        $entityManager = $this->getDoctrine()->getManager();
        $category = $this->getDoctrine()->getRepository(Categories::class);
        $category = $category->find($id);

        if (!$category) {
            throw $this->createNotFoundException(
                'No existe una categoría con el id: ' . $id
            );
        }

        $entityManager->remove($category);
        try {
            $entityManager->flush();
        } catch (\Throwable $th) {
            $this->addFlash('error','Error al eliminar la categoría. Hay que eliminar/cambiar de categoría los productos asociados a esta');
            return $this->redirectToRoute('app_category');
        }
        $this->addFlash('alert','Categoría eliminada con éxito');


        return $this->redirectToRoute('app_category');

    }

    public function update(Request $request, $id) {

        $category = $this->getDoctrine()->getRepository(Categories::class);
        $category = $category->find($id);

        if (!$category) {
            throw $this->createNotFoundException(
                'No existe una categoría con el id: ' . $id
            );
        }

        $form = $this->createFormBuilder($category)
            ->add('name', TextType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $category = $form->getData();
            $em->flush();

            $this->addFlash('alert','Categoría modificada con éxito');

            return $this->redirectToRoute('app_category');
        }

        return $this->render(
            'category/edit.html.twig',
            array('form' => $form->createView())
        );

    }

     
}
