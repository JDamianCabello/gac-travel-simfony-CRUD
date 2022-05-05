<?php

namespace App\Controller;

use App\Entity\User;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserController extends AbstractController
{
    public function index(): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();;

        return $this->render('user/index.html.twig', [
            'users' => $users
        ]);
    }

    public function create(Request $request) {

        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add('username', TextType::class)
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $user->setCreatedAt(new DateTimeImmutable(date('Y-m-d H:i:s')));
            $user->setActive(true);
            $user->setRoles(['ROLE_USER']);
            $factory = new PasswordHasherFactory([
                'common' => ['algorithm' => 'auto'],
                'memory-hard' => ['algorithm' => 'auto'],
            ]);
    
            $passwordHasher = $factory->getPasswordHasher('common');
            $hash = $passwordHasher->hash($form->get('plainPassword')->getData());
    
            $user->setPassword($hash);
            $em->persist($user);
            $em->flush();

            $this->addFlash('alert','Usuario creado con éxito');
            return $this->redirectToRoute('app_user');
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function delete($id) {

        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getDoctrine()->getRepository(User::class);
        $user = $user->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No existe un usuario con el id: ' . $id
            );
        }
        if($user === $this->getUser()){
            $this->addFlash('error','Error al eliminar el Usuario. No puedes eliminar el usuario con el que estas logueado.');
            return $this->redirectToRoute('app_user');
        }

        $entityManager->remove($user);
        try {
            $entityManager->flush();
        } catch (\Throwable $th) {
            $this->addFlash('error','Error al eliminar el Usuario. Hay que eliminar los registros del log para ello.');
            return $this->redirectToRoute('app_user');
        }

        $this->addFlash('alert','Usuario eliminado con éxito');


        return $this->redirectToRoute('app_user');

    }

    public function update(Request $request, $id) {

        $user = $this->getDoctrine()->getRepository(User::class);
        $user = $user->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No existe un usuario con el id: ' . $id
            );
        }

        $form = $this->createFormBuilder($user)
            ->add('username', TextType::class)
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('active', CheckboxType::class,[
                'label'    => '¿El usuario está activo?',
                'required' => false,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $user = $form->getData();
            $user->setRoles(['ROLE_USER']);
            $factory = new PasswordHasherFactory([
                'common' => ['algorithm' => 'auto'],
                'memory-hard' => ['algorithm' => 'auto'],
            ]);
    
            $passwordHasher = $factory->getPasswordHasher('common');
            $hash = $passwordHasher->hash($form->get('plainPassword')->getData());
    
            $user->setActive($form->get('active')->getData());
            $user->setPassword($hash);
            $em->flush();

            $this->addFlash('alert','Usuario modificado con éxito');

            return $this->redirectToRoute('app_user');
        }

        return $this->render(
            'user/edit.html.twig',
            array('form' => $form->createView())
        );

    }
}
