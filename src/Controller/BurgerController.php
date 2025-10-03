<?php

namespace App\Controller;

use App\Entity\Pain;
use App\Entity\Image;
use App\Entity\Burger;
use App\Entity\Oignon; 
use App\Entity\Sauce; 

use App\Repository\BurgerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


#[Route('/burger', name: 'burger_')]
class BurgerController extends AbstractController
{
    #[Route('/list', name: 'list')]
    public function list(EntityManagerInterface $entityManager): Response 
    {
  
        $repository = $entityManager->getRepository(Burger::class);
        $burgers = $repository->findAll(); 

        //dd($burgers);

   
        return $this->render('burger/list_burger.html.twig', [
            'burgers' => $burgers, 
        ]);
    }

    #[Route('/show/{id}', name: 'detail')]
    public function detail(int $id, EntityManagerInterface $entityManager): Response
    {
        $repository = $entityManager->getRepository(Burger::class);
        $burger = $repository->find($id);

        // $burgers = [
        //     1 => [
        //         'name' => 'Cheeseburger',
        //         'description' => 'Un délicieux cheeseburger avec du cheddar fondant.',
        //         'price' => 5.99,
        //         'image' => 'burger.jpg'
        //     ],
        //     2 => [
        //         'name' => 'Bacon Burger',
        //         'description' => 'Burger avec bacon croustillant et sauce BBQ.',
        //         'price' => 6.99,
        //         'image' => 'burger.jpg'
        //     ],
        //     3 => [
        //         'name' => 'Veggie Burger',
        //         'description' => 'Burger végétarien avec galette de légumes maison.',
        //         'price' => 5.49,
        //         'image' => 'burger.jpg'
        //     ]
        // ];
        if(!$burger){
            return new Response('Aucun burger trouvé pour l\'id ' . $id);
        }
        //dd($burgers)

        // $burger = $burgers[$id];

        return $this->render('burger/detail.html.twig', [
            'burger' => $burger
        ]);
    }


    #[Route('/create/{name}/{price}/{painId}/{imageId}', name: 'burger_create_dynamic')]
    public function create(
        EntityManagerInterface $entityManager,
        string $name,
        float $price,
        int $painId,
        int $imageId
        // Vous pouvez ajouter des paramètres pour les IDs des Oignons et Sauces si vous le souhaitez
    ): Response {
        // Récupérer les entités liées par leurs IDs
        $pain = $entityManager->getRepository(Pain::class)->find($painId);
        $image = $entityManager->getRepository(Image::class)->find($imageId);

        // Récupérer des oignons et sauces (par exemple, les 2 premiers)
        $oignonRepository = $entityManager->getRepository(Oignon::class);
        $sauceRepository = $entityManager->getRepository(Sauce::class);
        $oignons = $oignonRepository->findBy([], [], 2);
        $sauces = $sauceRepository->findBy([], [], 2);


        // Vérifier si les entités obligatoires ont été trouvées
        if (!$pain || !$image) {
             // Vous pourriez renvoyer un message d'erreur plus spécifique ici
            throw $this->createNotFoundException('Impossible de trouver le pain ou l\'image spécifié(e).');
        }

        $burger = new Burger();
        $burger->setName($name);
        $burger->setPrice($price);
        $burger->setPain($pain);
        $burger->setImage($image);

        // Ajouter les oignons et sauces trouvés (comme dans votre code original)
        foreach ($oignons as $oignon) {
            $burger->addOignon($oignon);
        }

        foreach ($sauces as $sauce) {
            $burger->addSauce($sauce);
        }

        $entityManager->persist($burger);
        $entityManager->flush();

        $this->addFlash('success', 'Le burger "' . $name . '" a été créé avec succès !');

        // Rediriger vers la liste des burgers (ou une autre page appropriée)
        return $this->redirectToRoute('burger_list'); // Utiliser le nom de route défini dans ce contrôleur

    }


        #[Route('/update/{id}', name: 'app_burger_update')]
      
        public function update(EntityManagerInterface $entityManager, int $id): Response
        {
           
            $repository = $entityManager->getRepository(Burger::class);
            $burger = $repository->find($id); 
    
            if (!$burger) {
                throw $this->createNotFoundException('Aucun burger trouvé pour l\'id ' . $id);
            }
    
            $burger->setName('Buffalo Springfield update'); 
            $burger->setPrice(99.99);               
            $entityManager->flush(); 
    
            return new Response('Burger avec l\'id ' . $id . ' modifié avec succès !');
        }

        #[Route('/delete/{id}', name: 'app_burger_delete')]
        public function delete(EntityManagerInterface $entityManager, int $id): Response
        {
            $repository = $entityManager->getRepository(Burger::class);
            $burger = $repository->find($id); 
    
            if (!$burger) {
                throw $this->createNotFoundException('Aucun burger trouvé pour l\'id ' . $id);
            }
    
            $entityManager->remove($burger);
            $entityManager->flush(); 
    
            return new Response('Burger avec l\'id ' . $id . ' supprimé avec succès !');
        }

        #[Route('/ingredient/{ingredientType}/{ingredientName}', name:'list_ingredient')]
        public function listByIngredient(
            string $ingredientType,
            string $ingredientName,
            BurgerRepository $burgerRepository
            // EntityManagerInterface $entityManager is not needed here unless you perform other operations
        ): Response {
            // Map the ingredient type string from the URL to the actual entity class
            $ingredientEntityClass = match ($ingredientType) {
                'oignon' => Oignon::class,
                'sauce' => Sauce::class,
                'pain' => Pain::class,
                default => throw $this->createNotFoundException(sprintf('Unknown ingredient type "%s".', $ingredientType)),
            };
    
            try {
                // Call the generic findBurgersWithIngredient method from the repository
                $burgers = $burgerRepository->findBurgersWithIngredient($ingredientEntityClass, $ingredientName);
            } catch (\InvalidArgumentException $e) {
                // Handle the case where the relation property is not found in the Burger entity
                $this->addFlash('error', $e->getMessage());
                // Redirect to a suitable page, e.g., the homepage or a burger list page
                return $this->redirectToRoute('burger_list'); // Redirecting to the general burger list
            }
    
            // Render a Twig template to display the results
            return $this->render('burger/list_ingredient.html.twig', [
                'burgers' => $burgers,
                'ingredient_name' => $ingredientName,
                'ingredient_type' => ucfirst($ingredientType), // Capitalize for display
            ]);
        }
    
        #[Route('/top-burgers/{limit<\d+>}', name:'top_burgers')]
        public function topBurgers(
            int $limit, // Symfony va automatiquement convertir la partie de l'URL en entier
            BurgerRepository $burgerRepository
        ): Response {
            // Récupérer les X burgers les plus chers en utilisant la nouvelle méthode
            $topBurgers = $burgerRepository->findTopXBurgers($limit);

            // Renvoyer une vue pour afficher ces burgers
            return $this->render('burger/top_burgers.html.twig', [
                'burgers' => $topBurgers,
                'limit' => $limit,
            ]);
        }

        #[Route('/without-ingredient/{ingredientType}/{ingredientName}', name:'burgers_without_ingredient')]
        public function listBurgersWithoutIngredient(
            string $ingredientType,
            string $ingredientName,
            BurgerRepository $burgerRepository,
            EntityManagerInterface $entityManager 
        ): Response {
        
            $ingredientEntityClass = match ($ingredientType) {
                'oignon' => Oignon::class,
                'sauce' => Sauce::class,
                'pain' => Pain::class,
                default => throw $this->createNotFoundException(sprintf('Type d\'ingrédient inconnu "%s".', $ingredientType)),
            };

            $ingredientRepository = $entityManager->getRepository($ingredientEntityClass);
            $ingredient = $ingredientRepository->findOneBy(['name' => $ingredientName]);

            if (!$ingredient) {
                throw $this->createNotFoundException(sprintf('Ingrédient "%s" de type "%s" non trouvé.', $ingredientName, $ingredientType));
            }
            try {
                $burgers = $burgerRepository->findBurgersWithoutIngredient($ingredient);
                return $this->render('burger/list_without_ingredient.html.twig', [
                    'burgers' => $burgers,
                    'ingredient_type' => $ingredientType,
                    'ingredient_name' => $ingredientName,
                ]);
            } catch (\InvalidArgumentException $e) {
                throw $this->createNotFoundException($e->getMessage());
            }
        }

        #[Route('/min-ingredients/{minIngredients<\d+>}', name: 'min_ingredients')]
        public function listBurgersWithMinIngredients(BurgerRepository $burgerRepository, int $minIngredients): Response
        {
            $burgers = $burgerRepository->findBurgersWithMinimumIngredients($minIngredients);
    
            return $this->render('burger/list_with_min_ingredients.html.twig', [
                'burgers' => $burgers,
                'minIngredients' => $minIngredients,
            ]);
        }

}



?>