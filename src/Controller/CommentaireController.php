<?php

namespace App\Controller;

use App\Entity\Commentaire; 
use Doctrine\ORM\EntityManagerInterface; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Burger;
use App\Entity\Image;
use App\Entity\Oignon;
use App\Entity\Pain;
use App\Entity\Sauce;


final class CommentaireController extends AbstractController
{
 
    #[Route('/commentaire/list', name: 'app_commentaire_list')] 
    public function list(EntityManagerInterface $entityManager): Response 
    {

        $repository = $entityManager->getRepository(Commentaire::class); 
        $commentaires = $repository->findAll(); 
        dd($commentaires);

        return new Response('Liste des Commentaires (voir le dump ci-dessus)');
    }


    #[Route('/commentaire', name: 'app_commentaire')]
    public function index(): Response
    {
        return $this->render('commentaire/index.html.twig', [
            'controller_name' => 'CommentaireController',
        ]);
    }

    #[Route('/commentaire/create/{burgerId}', name: 'app_commentaire_create', methods: ['GET', 'POST'])] // Add burgerId to the route
    public function create(Request $request, EntityManagerInterface $entityManager, int $burgerId): Response
    {
        // Retrieve the Burger the comment will be associated with
        $burgerRepository = $entityManager->getRepository(Burger::class);
        $burger = $burgerRepository->find($burgerId);

        if (!$burger) {
            throw $this->createNotFoundException('No burger found for id ' . $burgerId);
        }

        $commentaire = new Commentaire();
        $commentaire->setBurger($burger); // Associate the comment with the burger

        // TODO: Implement form handling in Exercise 4
        // $form = $this->createForm(CommentaireType::class, $commentaire);
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
            // TODO: Add form submission and validation logic here in Exercise 4

            // For now, manually set some data for testing
            // $commentaire->setContenu('This is a test comment.');
            // $commentaire->setCreatedAt(new \DateTimeImmutable());
            // $commentaire->setAuteur('Anonymous'); // Or get from logged-in user


            // For now, just persist and flush (remove this or adjust later with form logic)
            // $entityManager->persist($commentaire);
            // $entityManager->flush();

            // return $this->redirectToRoute('app_burger_detail', ['id' => $burger->getId()]); // Redirect back to the burger detail page
        // }

        // TODO: Render a template with the form in Exercise 4
        return $this->render('commentaire/create.html.twig', [
            'controller_name' => 'CommentaireController',
            'burger' => $burger,
            // TODO: Pass the form view to the template in Exercise 4
        ]);
    }

    #[Route('/commentaire/update/{id}', name: 'app_commentaire_update', methods: ['GET', 'POST'])]
    public function update(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $repository = $entityManager->getRepository(Commentaire::class);
        $commentaire = $repository->find($id);

        if (!$commentaire) {
            throw $this->createNotFoundException('No comment found for id ' . $id);
        }

        // TODO: Implement form handling in Exercise 4
        // $form = $this->createForm(CommentaireType::class, $commentaire); // Reuse the same form type
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
            // TODO: Add form submission and validation logic here in Exercise 4

            // $entityManager->flush(); // Persist the changes

            // return $this->redirectToRoute('app_burger_detail', ['id' => $commentaire->getBurger()->getId()]); // Redirect back to the associated burger
        // }

        // TODO: Render a template with the form in Exercise 4
        return $this->render('commentaire/update.html.twig', [
            'commentaire' => $commentaire,
            'controller_name' => 'CommentaireController',
            // TODO: Pass the form view to the template in Exercise 4
        ]);
    }

    #[Route('/commentaire/delete/{id}', name: 'app_commentaire_delete', methods: ['POST'])] // Often a POST request for safety
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $repository = $entityManager->getRepository(Commentaire::class);
        $commentaire = $repository->find($id);

        if (!$commentaire) {
            throw $this->createNotFoundException('No comment found for id ' . $id);
        }

        $entityManager->remove($commentaire);
        $entityManager->flush();

        // TODO: Redirect back to the associated burger or a comment list page
        // return $this->redirectToRoute('app_burger_detail', ['id' => $commentaire->getBurger()->getId()]);
        return new Response('Comment with id ' . $id . ' deleted successfully.');
    }
    



}
