<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Image;

final class ImageController extends AbstractController
{
  
    #[Route('/image/list', name: 'app_image_list')] 
    public function list(EntityManagerInterface $entityManager): Response 
    {
        $repository = $entityManager->getRepository(Image::class);
        $images = $repository->findAll();
        dd($images);

        return new Response('Liste des Images (voir le dump ci-dessus)');
    }


    #[Route('/image', name: 'app_image')]
    public function index(): Response
    {
        return $this->render('image/index.html.twig', [
            'controller_name' => 'ImageController',
        ]);
    }

    #[Route('/image/create', name: 'app_image_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $image = new Image();
        // You would typically create a form here to handle image data and file uploads
        // $form = $this->createForm(ImageType::class, $image);
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload if applicable
            // $file = $form->get('file')->getData();
            // if ($file) {
                // Process and save the file
                // $newFilename = uniqid().'.'.$file->guessExtension();
                // try {
                    // $file->move(
                        // $this->getParameter('images_directory'), // Define this parameter in your config
                        // $newFilename
                    // );
                // } catch (FileException $e) {
                    // Handle exception
                // }
                // $image->setUrl($newFilename);
            // }

            // $entityManager->persist($image);
            // $entityManager->flush();

            // return $this->redirectToRoute('app_image_list'); // Redirect to image list after creation
        // }

        // return $this->render('image/create.html.twig', [
        //     // 'form' => $form->createView(),
        //     'controller_name' => 'ImageController',
        // ]);

        $imageName = $request->query->get('name', 'Nouvelle Image'); // Récupérer le nom de la requête GET, par défaut "Nouvelle Image"
        $imageUrl = $request->query->get('url', 'burger.jpg');   // Récupérer l'URL de la requête GET, par défaut "nouvelle_image.jpg"

        $image->setName($imageName);
        $image->setUrl($imageUrl);

        $entityManager->persist($image);
        $entityManager->flush();

        return $this->redirectToRoute('app_image_list');
    }

    #[Route('/image/update/{id}', name: 'app_image_update', methods: ['GET', 'POST'])]
    public function update(Request $request, EntityManagerInterface $entityManager, int $id): Response
    {
        $repository = $entityManager->getRepository(Image::class);
        $image = $repository->find($id);

        if (!$image) {
            throw $this->createNotFoundException('No image found for id ' . $id);
        }

        // You would typically create a form here to handle image data and file uploads for update
        // $form = $this->createForm(ImageType::class, $image); // Reuse the same form type
        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload if a new file is uploaded
            // ... (similar logic to the create function)

            // $entityManager->flush(); // Persist the changes

            // return $this->redirectToRoute('app_image_list'); // Redirect after update
        // }

        // Render the form
        // return $this->render('image/update.html.twig', [
        //     'image' => $image,
        //     // 'form' => $form->createView(),
        //     'controller_name' => 'ImageController',
        // ]);
       

        return $this->redirectToRoute('app_image_list');
    }


    #[Route('/image/delete/{id}', name: 'app_image_delete', methods: ['POST'])] // Often a POST request for safety
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
        $repository = $entityManager->getRepository(Image::class);
        $image = $repository->find($id);

        if (!$image) {
            throw $this->createNotFoundException('No image found for id ' . $id);
        }

        $entityManager->remove($image);
        $entityManager->flush();

        return new Response('Image with id ' . $id . ' deleted successfully.');
    }




}
